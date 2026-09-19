<?php

namespace App\Services\Email;

readonly class EmailRiskResult
{
    public function __construct(
        public int $score,
        public array $matchedRules,
        public string $severity,
    ) {}

    public function isFlagged(): bool
    {
        return $this->score >= EmailClassifier::FLAG_THRESHOLD;
    }
}
