<?php

namespace Tests\Unit\Support;

use App\Services\MaxMindLocationResolver;
use Tests\TestCase;

class LocationResolverTest extends TestCase
{
    private function resolver(string $database): MaxMindLocationResolver
    {
        config()->set('geoip.database', $database);

        return new MaxMindLocationResolver;
    }

    public function test_returns_null_for_private_and_invalid_ips(): void
    {
        $resolver = $this->resolver('/tmp/does-not-exist.mmdb');

        $this->assertNull($resolver->resolve(null));
        $this->assertNull($resolver->resolve(''));
        $this->assertNull($resolver->resolve('127.0.0.1'));
        $this->assertNull($resolver->resolve('10.0.0.5'));
        $this->assertNull($resolver->resolve('not-an-ip'));
    }

    public function test_returns_null_when_database_is_missing(): void
    {
        $this->assertNull($this->resolver('/tmp/does-not-exist.mmdb')->resolve('8.8.8.8'));
    }

    public function test_resolves_country_for_public_ip(): void
    {
        $database = (string) config('geoip.database');

        if (! is_file($database)) {
            $this->markTestSkipped('GeoIP database not present.');
        }

        $location = $this->resolver($database)->resolve('103.86.96.100');

        $this->assertIsString($location);
        $this->assertStringContainsString('Australia', $location);
    }
}
