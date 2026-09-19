<?php

namespace App\Services\Email;

/**
 * Narrow email risk classification: "does this message request a
 * payment-detail change", not general fraud judgment. Swappable — an
 * LLM-backed implementation can replace the rules engine without
 * touching callers.
 */
interface EmailClassifier
{
    public const FLAG_THRESHOLD = 50;

    public function classify(string $subject, string $body): EmailRiskResult;
}
