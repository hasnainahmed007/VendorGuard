<?php

namespace App\Support;

class DeviceDetector
{
    public static function detect(?string $userAgent): string
    {
        $agent = (string) $userAgent;

        if ($agent === '') {
            return 'Unknown';
        }

        $os = 'Unknown';
        $client = 'Unknown';

        if (str_contains($agent, 'iPhone') || str_contains($agent, 'iPad')) {
            $os = str_contains($agent, 'iPad') ? 'iPadOS' : 'iOS';
        } elseif (str_contains($agent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($agent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($agent, 'Mac OS X') || str_contains($agent, 'Macintosh')) {
            $os = 'macOS';
        } elseif (str_contains($agent, 'Linux')) {
            $os = 'Linux';
        }

        if (str_contains($agent, 'CashPilot')) {
            $client = 'CashPilot App';
        } elseif (str_contains($agent, 'Edg/')) {
            $client = 'Edge';
        } elseif (str_contains($agent, 'Chrome/')) {
            $client = $os === 'iOS' || $os === 'iPadOS' ? 'Chrome · iOS' : 'Chrome';
        } elseif (str_contains($agent, 'Firefox/')) {
            $client = 'Firefox';
        } elseif (str_contains($agent, 'Safari/') && ! str_contains($agent, 'Chrome')) {
            $client = 'Safari';
        } elseif (str_contains($agent, 'okhttp') || str_contains($agent, 'Dart/')) {
            $client = 'Mobile App';
        }

        if ($client === 'Unknown' && $os !== 'Unknown') {
            return $os;
        }

        if ($os === 'Unknown') {
            return $client;
        }

        if (str_contains($client, 'iOS')) {
            return $client;
        }

        return $client.' · '.$os;
    }
}
