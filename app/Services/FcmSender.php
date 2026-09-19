<?php

namespace App\Services;

interface FcmSender
{
    public function sendToToken(string $token, string $title, string $body, array $data = []): FcmResult;
}
