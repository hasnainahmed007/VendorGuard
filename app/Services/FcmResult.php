<?php

namespace App\Services;

class FcmResult
{
    public function __construct(
        public readonly bool $ok,
        public readonly ?string $messageId = null,
        public readonly ?string $error = null,
        public readonly bool $invalidToken = false,
    ) {}

    public static function success(string $messageId): self
    {
        return new self(true, $messageId);
    }

    public static function failure(string $error, bool $invalidToken = false): self
    {
        return new self(false, null, $error, $invalidToken);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ok' => $this->ok,
            'message_id' => $this->messageId,
            'error' => $this->error,
            'invalid_token' => $this->invalidToken,
        ];
    }
}
