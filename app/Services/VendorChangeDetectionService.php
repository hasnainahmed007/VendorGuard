<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Vendor;
use App\Models\VendorChangeLog;
use App\Services\Email\EmailClassifier;
use App\Support\BankDetailHasher;

class VendorChangeDetectionService
{
    public function __construct(private IncidentService $incidents) {}

    /**
     * Record a detected payment-detail change and open its incident.
     *
     * Idempotent on raw_source_ref: accounting/email watchers pass their
     * source-system identifier (webhook id, poll fingerprint) so duplicate
     * deliveries never open duplicate incidents.
     *
     * @param  array{field_changed: string, old_value_hash?: ?string, new_value_hash?: ?string, source?: string, raw_source_ref?: ?string, detected_at?: ?string}  $change
     */
    public function recordChange(Vendor $vendor, array $change, ?string $severity = null): Incident
    {
        $source = $change['source'] ?? 'manual';
        $ref = $change['raw_source_ref'] ?? null;

        if ($ref !== null) {
            $existing = VendorChangeLog::where('vendor_id', $vendor->getKey())
                ->where('raw_source_ref', $ref)
                ->first();

            if ($existing?->incident !== null) {
                return $existing->incident;
            }
        }

        $log = VendorChangeLog::create([
            'tenant_id' => $vendor->tenant_id,
            'vendor_id' => $vendor->getKey(),
            'field_changed' => $change['field_changed'],
            'old_value_hash' => $change['old_value_hash'] ?? null,
            'new_value_hash' => $change['new_value_hash'] ?? null,
            'source' => $source,
            'raw_source_ref' => $ref,
            'detected_at' => $change['detected_at'] ?? now(),
        ]);

        if ($log->isBankDetailChange()) {
            $vendor->update(['payment_hold' => true]);
        }

        return $this->incidents->openFromChangeLog(
            $log,
            $severity ?? ($log->isBankDetailChange() ? 'high' : 'medium')
        );
    }

    /**
     * Match a vendor by name mention (longest name first). Used to attach
     * inbound emails to vendors when no explicit vendor context exists.
     */
    public function matchVendorByText(string $text): ?Vendor
    {
        $vendors = Vendor::orderByRaw('CHAR_LENGTH(name) DESC')->get(['id', 'name']);

        foreach ($vendors as $vendor) {
            if (stripos($text, $vendor->name) !== false) {
                return $vendor;
            }
        }

        return null;
    }

    /**
     * Flag a brand-new vendor whose creation note carries bank-detail-change
     * language (e.g. "use this account, not our usual one"). Raises risk
     * and opens an incident; benign notes record nothing.
     */
    public function flagNewVendor(
        Vendor $vendor,
        string $note,
        EmailClassifier $classifier,
        string $source = 'manual'
    ): ?Incident {
        $result = $classifier->classify($vendor->name, $note);

        if (! $result->isFlagged()) {
            return null;
        }

        $vendor->update(['risk_score' => max($vendor->risk_score, $result->score)]);

        return $this->recordChange($vendor, [
            'field_changed' => 'first_invoice_note',
            'old_value_hash' => null,
            'new_value_hash' => hash('sha256', $note),
            'source' => $source,
            'raw_source_ref' => null,
        ], $result->severity);
    }

    /**
     * Compare fresh bank details against the stored hashes. Returns the
     * opened incident, or null when nothing changed.
     *
     * Only diverges from already-known values fire incidents: a vendor
     * with no stored hashes yet (first sync) records nothing here — the
     * watcher populates baseline hashes silently instead.
     */
    public function checkBankDetails(
        Vendor $vendor,
        ?string $bankAccount,
        ?string $routingNumber,
        string $source,
        ?string $ref = null
    ): ?Incident {
        $last4 = BankDetailHasher::last4($bankAccount);

        if ($last4 !== null
            && $vendor->current_bank_last4 !== null
            && $last4 !== $vendor->current_bank_last4) {
            return $this->recordChange($vendor, [
                'field_changed' => 'bank_account',
                'old_value_hash' => 'last4:'.$vendor->current_bank_last4,
                'new_value_hash' => 'last4:'.$last4,
                'source' => $source,
                'raw_source_ref' => $ref,
            ]);
        }

        $routingHash = BankDetailHasher::hash($routingNumber);

        if ($routingHash !== null && $routingHash !== $vendor->current_routing_hash) {
            return $this->recordChange($vendor, [
                'field_changed' => 'routing_number',
                'old_value_hash' => $vendor->current_routing_hash,
                'new_value_hash' => $routingHash,
                'source' => $source,
                'raw_source_ref' => $ref,
            ]);
        }

        return null;
    }
}
