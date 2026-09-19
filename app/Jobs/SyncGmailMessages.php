<?php

namespace App\Jobs;

use App\Models\Integration;
use App\Models\IntegrationSyncLog;
use App\Models\Tenant;
use App\Services\Email\GmailClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncGmailMessages implements ShouldQueue
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
    public function handle(GmailClient $client): void
    {
        if (! $client->isConfigured()) {
            return;
        }

        $integrations = Integration::withoutTenancy()
            ->where('provider', 'gmail')
            ->where('status', 'connected')
            ->get();

        foreach ($integrations as $integration) {
            $this->syncIntegration($integration, $client);
        }
    }

    private function syncIntegration(Integration $integration, GmailClient $client): void
    {
        try {
            foreach ($client->listMessages($integration) as $gmailId) {
                $ref = "gmail:{$integration->external_account_id}:{$gmailId}";

                $seen = IntegrationSyncLog::withoutTenancy()
                    ->where('integration_id', $integration->getKey())
                    ->where('payload_hash', $ref)
                    ->exists();

                if ($seen) {
                    continue;
                }

                $message = $client->getMessage($integration, $gmailId);

                ProcessInboundEmail::dispatch(
                    $integration->tenant_id,
                    null,
                    $message['subject'],
                    $message['body'],
                    $message['from'],
                    $ref,
                    $integration->getKey(),
                );

                IntegrationSyncLog::create([
                    'tenant_id' => $integration->tenant_id,
                    'integration_id' => $integration->getKey(),
                    'event_type' => 'email.queued',
                    'payload_hash' => $ref,
                    'status' => 'ok',
                ]);
            }
        } catch (Throwable $e) {
            report($e);

            $tenant = Tenant::find($integration->tenant_id);

            if ($tenant !== null) {
                tenancy()->initialize($tenant);

                try {
                    IntegrationSyncLog::create([
                        'tenant_id' => $integration->tenant_id,
                        'integration_id' => $integration->getKey(),
                        'event_type' => 'email.pull',
                        'payload_hash' => null,
                        'status' => 'failed',
                    ]);
                } finally {
                    tenancy()->end();
                }
            }
        }
    }
}
