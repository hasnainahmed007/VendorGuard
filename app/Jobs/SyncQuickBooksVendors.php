<?php

namespace App\Jobs;

use App\Models\Integration;
use App\Models\IntegrationSyncLog;
use App\Models\Tenant;
use App\Models\Vendor;
use App\Services\QuickBooks\QuickBooksClient;
use App\Services\VendorChangeDetectionService;
use App\Support\BankDetailHasher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncQuickBooksVendors implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    /**
     * Execute the job.
     */
    public function handle(QuickBooksClient $client, VendorChangeDetectionService $detection): void
    {
        if (! $client->isConfigured()) {
            return;
        }

        $integrations = Integration::withoutTenancy()
            ->where('provider', 'quickbooks')
            ->where('status', 'connected')
            ->get();

        foreach ($integrations as $integration) {
            $this->syncIntegration($integration, $client, $detection);
        }
    }

    private function syncIntegration(
        Integration $integration,
        QuickBooksClient $client,
        VendorChangeDetectionService $detection
    ): void {
        $tenant = Tenant::find($integration->tenant_id);

        if ($tenant === null) {
            return;
        }

        try {
            tenancy()->initialize($tenant);

            $vendors = $client->listVendors($integration);

            foreach ($vendors as $row) {
                $this->syncVendorRow($integration, $row, $detection);
            }

            $this->writeLog($integration, 'vendor.pull', null, 'ok');
        } catch (Throwable $e) {
            report($e);

            $this->writeLog(
                $integration,
                'vendor.pull',
                hash('sha256', $e->getMessage()),
                'failed'
            );
        } finally {
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }

    /**
     * @param  array{external_id: string, name: string, bank_account: ?string, routing_number: ?string}  $row
     */
    private function syncVendorRow(
        Integration $integration,
        array $row,
        VendorChangeDetectionService $detection
    ): void {
        $refBase = "qb:{$integration->external_account_id}:{$row['external_id']}";

        $vendor = Vendor::where('provider', 'quickbooks')
            ->where('external_id', $row['external_id'])
            ->first();

        if ($vendor === null) {
            // First sync establishes the baseline silently — a brand-new
            // vendor is not itself a change.
            Vendor::create([
                'tenant_id' => $integration->tenant_id,
                'provider' => 'quickbooks',
                'external_id' => $row['external_id'],
                'name' => $row['name'],
                'current_bank_last4' => BankDetailHasher::last4($row['bank_account']),
                'current_routing_hash' => BankDetailHasher::hash($row['routing_number']),
            ]);

            return;
        }

        if ($vendor->name !== $row['name']) {
            $vendor->update(['name' => $row['name']]);
        }

        $incident = $detection->checkBankDetails(
            $vendor,
            $row['bank_account'],
            $row['routing_number'],
            'accounting',
            $refBase.':'.hash('sha256', ($row['bank_account'] ?? '').'|'.($row['routing_number'] ?? ''))
        );

        if ($incident !== null) {
            $this->writeLog($integration, 'vendor.change', $refBase, 'ok');
        }
    }

    private function writeLog(
        Integration $integration,
        string $eventType,
        ?string $payloadHash,
        string $status
    ): void {
        IntegrationSyncLog::create([
            'tenant_id' => $integration->tenant_id,
            'integration_id' => $integration->getKey(),
            'event_type' => $eventType,
            'payload_hash' => $payloadHash,
            'status' => $status,
        ]);
    }
}
