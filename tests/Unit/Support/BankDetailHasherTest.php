<?php

namespace Tests\Unit\Support;

use App\Support\BankDetailHasher;
use Tests\TestCase;

class BankDetailHasherTest extends TestCase
{
    public function test_normalize_strips_non_digits(): void
    {
        $this->assertSame('021000021', BankDetailHasher::normalize('0210-00021'));
        $this->assertSame('1234', BankDetailHasher::normalize(' 12 34 '));
        $this->assertNull(BankDetailHasher::normalize(null));
        $this->assertNull(BankDetailHasher::normalize(''));
        $this->assertNull(BankDetailHasher::normalize('---'));
    }

    public function test_hash_is_deterministic_and_format_agnostic(): void
    {
        $this->assertSame(
            BankDetailHasher::hash('021000021'),
            BankDetailHasher::hash('0210-00021')
        );

        $this->assertNotSame(
            BankDetailHasher::hash('021000021'),
            BankDetailHasher::hash('021000022')
        );

        $this->assertNull(BankDetailHasher::hash(null));
        $this->assertNull(BankDetailHasher::hash(''));
    }

    public function test_last4_returns_only_last_four_digits(): void
    {
        $this->assertSame('9012', BankDetailHasher::last4('123456789012'));
        $this->assertSame('9012', BankDetailHasher::last4('1234-5678-9012'));
        $this->assertNull(BankDetailHasher::last4(null));
    }
}
