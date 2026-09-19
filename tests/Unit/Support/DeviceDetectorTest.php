<?php

namespace Tests\Unit\Support;

use App\Support\DeviceDetector;
use PHPUnit\Framework\TestCase;

class DeviceDetectorTest extends TestCase
{
    public function test_detects_chrome_on_windows(): void
    {
        $this->assertSame(
            'Chrome · Windows',
            DeviceDetector::detect('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36')
        );
    }

    public function test_detects_safari_on_macos(): void
    {
        $this->assertSame(
            'Safari · macOS',
            DeviceDetector::detect('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15')
        );
    }

    public function test_detects_flutter_app(): void
    {
        $this->assertSame('CashPilot App · Android', DeviceDetector::detect('CashPilot/1.0 (Android 14; Pixel 8)'));
    }

    public function test_unknown_agent(): void
    {
        $this->assertSame('Unknown', DeviceDetector::detect(null));
        $this->assertSame('Unknown', DeviceDetector::detect(''));
    }
}
