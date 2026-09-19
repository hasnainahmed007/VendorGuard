<?php

namespace App\Services\Alerts;

use Illuminate\Support\Str;

class LogWhatsAppSender implements WhatsAppSender
{
    /**
     * Send the message.
     */
    public function send(string $to, string $message): string
    {
        $id = 'log-'.Str::uuid()->toString();

        logger()->info('WhatsApp alert (log driver)', [
            'id' => $id,
            'to' => $to,
            'message' => $message,
        ]);

        return $id;
    }
}
