<?php

namespace App\Services;

use App\Events\IncidentCreated;
use App\Models\AuditTrail;
use App\Models\Incident;
use App\Models\User;
use App\Models\VendorChangeLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IncidentService
{
    public const TRANSITIONS = [
        'verified' => 'verified',
        'blocked' => 'blocked',
        'dismissed' => 'dismissed',
        'needs_info' => 'open',
    ];

    /**
     * Open an incident for a detected change and hold the vendor record.
     */
    public function openFromChangeLog(VendorChangeLog $changeLog, string $severity = 'medium'): Incident
    {
        return DB::transaction(function () use ($changeLog, $severity) {
            $incident = Incident::create([
                'tenant_id' => $changeLog->tenant_id,
                'vendor_id' => $changeLog->vendor_id,
                'change_log_id' => $changeLog->getKey(),
                'status' => 'open',
                'severity' => $severity,
            ]);

            $changeLog->vendor->update(['payment_hold' => true]);

            AuditTrail::create([
                'tenant_id' => $incident->tenant_id,
                'incident_id' => $incident->getKey(),
                'user_id' => null,
                'action' => 'incident.opened',
                'note' => "Detected {$changeLog->field_changed} change via {$changeLog->source}.",
            ]);

            IncidentCreated::dispatch($incident);

            return $incident->refresh();
        });
    }

    /**
     * Apply a verification-workflow action to an open incident.
     *
     * @throws ValidationException
     */
    public function transition(Incident $incident, string $action, User $by, ?string $note = null, ?int $assigneeId = null): Incident
    {
        if (! $incident->isOpen()) {
            throw ValidationException::withMessages([
                'action' => 'Only open incidents can be actioned.',
            ]);
        }

        if (! array_key_exists($action, self::TRANSITIONS)) {
            throw ValidationException::withMessages([
                'action' => 'Unknown incident action.',
            ]);
        }

        return DB::transaction(function () use ($incident, $action, $by, $note, $assigneeId) {
            $incident->status = self::TRANSITIONS[$action];
            $incident->resolution_note = $note;

            if ($action !== 'needs_info') {
                $incident->resolved_at = now();
            }

            if ($assigneeId !== null) {
                $incident->assigned_to_user_id = $assigneeId;
            }

            $incident->save();

            // Verified or dismissed vendors are safe to pay again; blocked
            // vendors stay on hold. needs_info leaves the hold untouched.
            // A verified change becomes the new baseline: adopt its hashes so
            // the next watcher run compares against the confirmed values.
            if ($action === 'verified') {
                $this->adoptChangeHashes($incident);
                $incident->vendor->update(['payment_hold' => false]);
            } elseif ($action === 'dismissed') {
                $incident->vendor->update(['payment_hold' => false]);
            }

            AuditTrail::create([
                'tenant_id' => $incident->tenant_id,
                'incident_id' => $incident->getKey(),
                'user_id' => $by->getKey(),
                'action' => "incident.{$action}",
                'note' => $note,
            ]);

            return $incident->refresh();
        });
    }

    /**
     * Adopt a verified change's hashes as the vendor's new baseline.
     */
    private function adoptChangeHashes(Incident $incident): void
    {
        $log = $incident->changeLog;

        if ($log === null || $log->new_value_hash === null) {
            return;
        }

        if ($log->field_changed === 'bank_account'
            && str_starts_with($log->new_value_hash, 'last4:')) {
            $incident->vendor->update([
                'current_bank_last4' => substr($log->new_value_hash, 6),
            ]);
        } elseif ($log->field_changed === 'routing_number') {
            $incident->vendor->update([
                'current_routing_hash' => $log->new_value_hash,
            ]);
        }
    }
}
