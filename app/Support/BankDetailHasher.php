<?php

namespace App\Support;

/**
 * Hash-only handling for bank account and routing numbers.
 *
 * The product only needs to answer "did it change", so raw numbers are
 * never persisted — only an HMAC hash (change detection) and the last
 * four digits (human recognition) are stored.
 */
final class BankDetailHasher
{
    /**
     * Normalize a number to digits only.
     */
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        return $digits === '' ? null : $digits;
    }

    /**
     * Deterministic HMAC-SHA256 hash used for change detection.
     */
    public static function hash(?string $value): ?string
    {
        $normalized = self::normalize($value);

        if ($normalized === null) {
            return null;
        }

        return hash_hmac('sha256', $normalized, (string) config('app.key'));
    }

    /**
     * Last four digits for display. Never store more than this.
     */
    public static function last4(?string $value): ?string
    {
        $normalized = self::normalize($value);

        if ($normalized === null) {
            return null;
        }

        return substr($normalized, -4);
    }
}
