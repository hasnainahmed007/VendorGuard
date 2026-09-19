<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Notifications\TenantBroadcastNotification;
use App\Services\FcmSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendTenantNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    /**
     * @var array<int, int>
     */
    public array $backoff = [1, 5, 10];

    public function __construct(
        public readonly int $notificationLogId,
    ) {}

    public function handle(FcmSender $sender): void
    {
        $log = NotificationLog::with('user')->find($this->notificationLogId);

        if ($log === null || $log->user === null) {
            return;
        }

        $wantsPush = in_array($log->channel, ['push', 'push_in_app'], true);
        $wantsInApp = in_array($log->channel, ['in_app', 'push_in_app'], true);

        $pushOk = ! $wantsPush;
        $pushMessageId = null;
        $pushError = null;

        if ($wantsPush) {
            $token = (string) ($log->user->fcm_token ?? '');

            if ($token === '') {
                $pushOk = false;
                $pushError = 'Recipient has no FCM token.';
            } else {
                $result = $sender->sendToToken($token, $log->title, $log->message, [
                    'log_id' => (string) $log->id,
                ]);

                if ($result->invalidToken) {
                    $log->user->forceFill(['fcm_token' => null])->save();
                }

                $pushOk = $result->ok;
                $pushMessageId = $result->messageId;
                $pushError = $result->error;
            }
        }

        if ($wantsInApp) {
            $log->user->notify(new TenantBroadcastNotification($log->title, $log->message, $log->channel));
        }

        $log->forceFill([
            'status' => $this->resolveStatus($wantsPush, $wantsInApp, $pushOk),
            'fcm_message_id' => $pushMessageId ?? $log->fcm_message_id,
            'error' => $pushError,
            'sent_at' => now(),
        ])->save();
    }

    public function failed(?Throwable $exception): void
    {
        NotificationLog::where('id', $this->notificationLogId)->update([
            'status' => 'failed',
            'error' => $exception?->getMessage(),
        ]);

        Log::error('Tenant notification delivery failed', [
            'notification_log_id' => $this->notificationLogId,
            'exception' => $exception?->getMessage(),
        ]);
    }

    private function resolveStatus(bool $wantsPush, bool $wantsInApp, bool $pushOk): string
    {
        if ($wantsPush && $wantsInApp) {
            return $pushOk ? 'sent' : 'partial';
        }

        if ($wantsPush) {
            return $pushOk ? 'sent' : 'failed';
        }

        return 'sent';
    }
}
