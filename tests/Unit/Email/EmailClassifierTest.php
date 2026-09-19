<?php

namespace Tests\Unit\Email;

use App\Services\Email\EmailClassifier;
use App\Services\Email\RuleBasedEmailClassifier;
use Tests\TestCase;

class EmailClassifierTest extends TestCase
{
    private EmailClassifier $classifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classifier = new RuleBasedEmailClassifier;
    }

    public function test_bank_change_email_is_flagged_critical(): void
    {
        $result = $this->classifier->classify(
            'Updated banking details for invoice #1042',
            'Hi, our bank details have changed. Please update our account and remit to the new account urgently, payment is overdue.'
        );

        $this->assertTrue($result->isFlagged());
        $this->assertSame('high', $result->severity);
        $this->assertGreaterThanOrEqual(EmailClassifier::FLAG_THRESHOLD, $result->score);
        $this->assertNotEmpty($result->matchedRules);
    }

    public function test_benign_email_stays_below_threshold(): void
    {
        $result = $this->classifier->classify(
            'Invoice #1042 attached',
            'Hi, please find attached our invoice for this month. Let me know if you have questions. Thanks!'
        );

        $this->assertFalse($result->isFlagged());
        $this->assertSame('low', $result->severity);
    }

    public function test_urgency_alone_does_not_flag(): void
    {
        $result = $this->classifier->classify(
            'Urgent: please review',
            'This is urgent, please review today. Asap would be great.'
        );

        // 15 (urgency) + 10 (payment_action? no) = urgency only: 15 < 50.
        $this->assertFalse($result->isFlagged());
    }

    public function test_bank_language_plus_urgency_reaches_high(): void
    {
        $result = $this->classifier->classify(
            'New account details',
            'Our new account details are attached. Please process payment immediately.'
        );

        $this->assertTrue($result->isFlagged());
        $this->assertContains('high', [$result->severity, 'critical']);
    }

    public function test_first_invoice_justification_pattern_matches(): void
    {
        $result = $this->classifier->classify(
            'First invoice',
            'Please use this account, not our usual one, going forward.'
        );

        $this->assertTrue($result->isFlagged());
    }
}
