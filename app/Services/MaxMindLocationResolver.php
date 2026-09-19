<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Throwable;

class MaxMindLocationResolver implements LocationResolver
{
    public function resolve(?string $ip): ?string
    {
        if ($ip === null || $ip === '') {
            return null;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return null;
        }

        $database = (string) config('geoip.database');

        if ($database === '' || ! is_file($database)) {
            return null;
        }

        try {
            $record = (new Reader($database))->city($ip);

            $parts = array_filter([$record->city->name, $record->country->name]);

            return $parts === [] ? null : implode(', ', $parts);
        } catch (AddressNotFoundException) {
            return null;
        } catch (Throwable) {
            return null;
        }
    }
}
