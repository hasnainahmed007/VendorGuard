<?php

namespace App\Services\Alerts;

use App\Mail\IncidentCreatedMail;
use App\Models\Incident;
use App\Models\IncidentNotification;
use App\Models\NotificationSetting;
use App\Models\TenantUserAccess;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class IncidentAlertService
{
    public function __construct(private WhatsAppSender $whatsapp) {}

    /**
     * Alert the tenant's actionable members about a new incident.
     *
     * Runs tenant-agnostic on purpose: the queued listener executes
     * without tenancy, so every query here is explicitly tenant-keyed.
     * Viewers are read-only and never alerted.
     */
    public function dispatchFor(Incident $incident): void
    {
        $settings = NotificationSetting::for($incident->tenant_id);

        foreach ($this->recipients($incident->tenant_id) as $user) {
            if ($settings->email_enabled) {
                $this->sendEmail($incident, $user);
            }
        }

        if ($settings->whatsapp_enabled && $settings->whatsapp_to !== null) {
            $this->sendWhatsApp($incident, $settings->whatsapp_to);
        }
    }

    /**
     * @return array<int, User>
     */
    private function recipients(string $tenantId): array
    {
        $homeUsers = User::withoutTenancy()
            ->where('tenant_id', $tenantId)
            ->whereRaw('LOWER(role) != ?', ['viewer'])
            ->get();

        $grantedIds = TenantUserAccess::withoutTenancy()
            ->where('tenant_id', $tenantId)
            ->whereRaw('LOWER(role) != ?', ['viewer'])
            ->pluck('user_id')
            ->all();

        $grantedUsers = $grantedIds === []
            ? collect()
            : User::withoutTenancy()->whereIn('id', $grantedIds)->get();

        return $homeUsers->concat($grantedUsers)->unique('id')->values()->all();
    }

    private function sendEmail(Incident $incident, User $user): void
    {
        $delivered = true;

        try {
            Mail::to($user->email)->send(new IncidentCreatedMail($incident));
        } catch (Throwable $e) {
            report($e);
            $delivered = false;
        }

        IncidentNotification::withoutTenancy()->create([
            'tenant_id' => $incident->tenant_id,
            'incident_id' => $incident->getKey(),
            'channel' => 'email',
            'sent_at' => now(),
            'delivered' => $delivered,
        ]);
    }

    private function sendWhatsApp(Incident $incident, string $to): void
    {
        $delivered = true;

        try {
            $incident->loadMissing('vendor');

            $this->whatsapp->send(
                $to,
                "VendorGuard: payment details changed for {$incident->vendor->name} — payment held. Call {$incident->vendor->verified_phone} to verify (incident #{$incident->getKey()})."
            );
        } catch (Throwable $e) {
            report($e);
            $delivered = false;
        }

        IncidentNotification::withoutTenancy()->create([
            'tenant_id' => $incident->tenant_id,
            'incident_id' => $incident->getKey(),
            'channel' => 'whatsapp',
            'sent_at' => now(),
            'delivered' => $delivered,
        ]);
    }
}
