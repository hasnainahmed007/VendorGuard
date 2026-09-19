<?php

namespace App\Jobs;

use App\Models\IntegrationSyncLog;
use App\Models\Tenant;
use App\Models\Vendor;
use App\Services\Email\EmailClassifier;
use App\Services\VendorChangeDetectionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessInboundEmail implements ShouldQueue
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
     * Create a new job instance.
     */
    public function __construct(
        public string $tenantId,
        public ?int $vendorId,
        public string $subject,
        public string $body,
        public string $from,
        public string $ref,
        public ?int $integrationId = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        EmailClassifier $classifier,
        VendorChangeDetectionService $detection
    ): void {
        $tenant = Tenant::find($this->tenantId);

        if ($tenant === null) {
            return;
        }

        tenancy()->initialize($tenant);

        try {
            $vendor = $this->vendorId !== null
                ? Vendor::find($this->vendorId)
                : $detection->matchVendorByText($this->subject."\n".$this->body);

            if ($vendor === null) {
                $this->writeLog('email.unmatched');

                return;
            }

            $result = $classifier->classify($this->subject, $this->body);

            if (! $result->isFlagged()) {
                return;
            }

            $detection->recordChange($vendor, [
                'field_changed' => 'email_message',
                'old_value_hash' => null,
                'new_value_hash' => hash('sha256', $this->subject."\n".$this->body),
                'source' => 'email',
                'raw_source_ref' => $this->ref,
            ], $result->severity);

            $this->writeLog('email.flagged');
        } finally {
            tenancy()->end();
        }
    }

    private function writeLog(string $eventType): void
    {
        if ($this->integrationId === null) {
            return;
        }

        IntegrationSyncLog::create([
            'tenant_id' => $this->tenantId,
            'integration_id' => $this->integrationId,
            'event_type' => $eventType,
            'payload_hash' => $this->ref,
            'status' => 'ok',
        ]);
    }
}
