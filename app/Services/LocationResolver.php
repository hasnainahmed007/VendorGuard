<?php

namespace App\Services;

interface LocationResolver
{
    /**
     * Resolve an IP address to a "City, Country" display string.
     */
    public function resolve(?string $ip): ?string;
}
