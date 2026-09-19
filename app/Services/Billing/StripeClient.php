<?php

namespace App\Services\Billing;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StripeClient
{
    private const API_URL = 'https://api.stripe.com/v1';

    /**
     * @return array<string, mixed>
     */
    private function config(): array
    {
        return config('services.stripe');
    }

    public function isConfigured(): bool
    {
        return $this->config()['secret'] !== null;
    }

    /**
     * Create a Checkout Session for the single MVP tier.
     *
     * @return array{id: string, url: string}
     */
    public function createCheckoutSession(
        string $customerEmail,
        string $priceId,
        string $successUrl,
        string $cancelUrl
    ): array {
        try {
            $response = Http::withBasicAuth($this->config()['secret'], '')
                ->asForm()
                ->post(self::API_URL.'/checkout/sessions', [
                    'mode' => 'subscription',
                    'customer_email' => $customerEmail,
                    'line_items[0][price]' => $priceId,
                    'line_items[0][quantity]' => 1,
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'subscription_data[trial_period_days]' => 14,
                ])
                ->throw()
                ->json();
        } catch (RequestException $e) {
            throw new RuntimeException('Stripe checkout creation failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response) || ! isset($response['id'], $response['url'])) {
            throw new RuntimeException('Stripe checkout creation returned an unexpected response.');
        }

        return ['id' => (string) $response['id'], 'url' => (string) $response['url']];
    }

    /**
     * Verify a Stripe webhook signature (tolerance 5 minutes).
     */
    public function verifySignature(string $payload, string $header): bool
    {
        $secret = $this->config()['webhook_secret'];

        if ($secret === null) {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $header) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

            if ($key === 't' && is_numeric($value)) {
                $timestamp = (int) $value;
            } elseif ($key === 'v1' && is_string($value)) {
                $signatures[] = $value;
            }
        }

        if ($timestamp === null || abs(time() - $timestamp) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }
}
