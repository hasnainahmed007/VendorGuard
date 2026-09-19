<?php

namespace App\Services\Alerts;

/**
 * WhatsApp delivery seam. The default LogWhatsAppSender records the
 * message; swap the container binding for a Twilio implementation
 * without touching callers.
 */
interface WhatsAppSender
{
    /**
     * Send the message. Returns the provider-side message id.
     */
    public function send(string $to, string $message): string;
}
