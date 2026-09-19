<?php

namespace App\Services\Email;

class RuleBasedEmailClassifier implements EmailClassifier
{
    /**
     * @var array<int, array{label: string, weight: int, patterns: array<int, string>}>
     */
    private const RULES = [
        [
            'label' => 'bank_change_language',
            'weight' => 40,
            'patterns' => [
                'bank details', 'banking details', 'bank detail',
                'new account', 'change of account', 'changed account',
                'updated account', 'update our account', 'update the account',
                'wire instructions', 'new wire', 'remit to', 'remittance',
                'routing number', 'sort code', 'iban', 'new bank',
                'bank account changed', 'payment details',
            ],
        ],
        [
            'label' => 'urgency_markers',
            'weight' => 15,
            'patterns' => [
                'urgent', 'asap', 'a.s.a.p', 'immediately', 'right away',
                'today', 'overdue', 'final notice', 'expires', 'deadline',
                'without delay',
            ],
        ],
        [
            'label' => 'payment_action_request',
            'weight' => 10,
            'patterns' => [
                'please update', 'kindly update', 'please remit',
                'process payment', 'release payment', 'hold payment',
                'confirm payment',
            ],
        ],
        [
            // A first-contact justification alone ("use this account, not our
            // usual one") is the exact shape of a new-vendor fraud attempt,
            // so it flags on its own. Patterns stay multi-word to avoid
            // matching everyday language.
            'label' => 'first_contact_justification',
            'weight' => 50,
            'patterns' => [
                'not our usual', 'instead of our usual', 'use this account',
                'our new details',
            ],
        ],
    ];

    public function classify(string $subject, string $body): EmailRiskResult
    {
        $text = mb_strtolower($subject."\n".$body);
        $score = 0;
        $matched = [];

        foreach (self::RULES as $rule) {
            foreach ($rule['patterns'] as $pattern) {
                if (str_contains($text, $pattern)) {
                    $score += $rule['weight'];
                    $matched[] = $rule['label'].':'.$pattern;
                    break;
                }
            }
        }

        $score = min(100, $score);

        return new EmailRiskResult(
            $score,
            $matched,
            match (true) {
                $score >= 80 => 'critical',
                $score >= 65 => 'high',
                $score >= EmailClassifier::FLAG_THRESHOLD => 'medium',
                default => 'low',
            }
        );
    }
}
