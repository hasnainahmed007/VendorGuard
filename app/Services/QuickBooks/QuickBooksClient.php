<?php

namespace App\Services\QuickBooks;

use App\Models\Integration;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class QuickBooksClient
{
    private const AUTH_URL = 'https://appcenter.intuit.com/connect/oauth2';

    private const TOKEN_URL = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';

    private const SANDBOX_API_URL = 'https://sandbox-quickbooks.api.intuit.com';

    private const PRODUCTION_API_URL = 'https://quickbooks.api.intuit.com';

    /**
     * @return array<string, mixed>
     */
    private function config(): array
    {
        return config('services.quickbooks');
    }

    public function isConfigured(): bool
    {
        $config = $this->config();

        return $config['client_id'] !== null
            && $config['client_secret'] !== null
            && $config['redirect'] !== null;
    }

    public function authorizationUrl(string $state): string
    {
        $config = $this->config();

        return self::AUTH_URL.'?'.http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect'],
            'response_type' => 'code',
            'scope' => $config['scopes'],
            'state' => $state,
        ]);
    }

    private function apiBaseUrl(): string
    {
        return $this->config()['sandbox'] ? self::SANDBOX_API_URL : self::PRODUCTION_API_URL;
    }

    /**
     * Exchange an authorization code for tokens.
     *
     * @return array{access_token: string, refresh_token?: string, expires_in?: int}
     */
    public function exchangeCode(string $code): array
    {
        $config = $this->config();

        try {
            $response = Http::asForm()
                ->withBasicAuth($config['client_id'], $config['client_secret'])
                ->post(self::TOKEN_URL, [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $config['redirect'],
                ])
                ->throw()
                ->json();
        } catch (RequestException $e) {
            throw new RuntimeException('QuickBooks token exchange failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response) || ! isset($response['access_token'])) {
            throw new RuntimeException('QuickBooks token exchange returned an unexpected response.');
        }

        return $response;
    }

    /**
     * @return array{access_token: string, refresh_token?: string, expires_in?: int}
     */
    public function refreshToken(string $refreshToken): array
    {
        $config = $this->config();

        try {
            $response = Http::asForm()
                ->withBasicAuth($config['client_id'], $config['client_secret'])
                ->post(self::TOKEN_URL, [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ])
                ->throw()
                ->json();
        } catch (RequestException $e) {
            throw new RuntimeException('QuickBooks token refresh failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response) || ! isset($response['access_token'])) {
            throw new RuntimeException('QuickBooks token refresh returned an unexpected response.');
        }

        return $response;
    }

    /**
     * Refresh the integration token when expired and persist the result.
     */
    public function ensureFreshToken(Integration $integration): Integration
    {
        if (! $integration->isExpired() || $integration->refresh_token === null) {
            return $integration;
        }

        $tokens = $this->refreshToken($integration->refresh_token);

        $integration->update([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'] ?? $integration->refresh_token,
            'expires_at' => isset($tokens['expires_in'])
                ? now()->addSeconds((int) $tokens['expires_in'])
                : null,
            'status' => 'connected',
        ]);

        return $integration->refresh();
    }

    /**
     * Pull vendors for the integration's company and normalize them.
     *
     * QuickBooks' core Vendor entity carries no dedicated bank-account
     * fields, so bank details are read from the optional BankAccountNumber /
     * RoutingNumber keys when a mapping provides them — otherwise null and
     * the watcher only tracks name/external identity for those vendors.
     *
     * @return array<int, array{external_id: string, name: string, bank_account: ?string, routing_number: ?string}>
     */
    public function listVendors(Integration $integration): array
    {
        $integration = $this->ensureFreshToken($integration->fresh() ?? $integration);

        try {
            $response = Http::withToken($integration->access_token)
                ->acceptJson()
                ->get($this->apiBaseUrl()."/v3/company/{$integration->external_account_id}/query", [
                    'query' => 'select * from Vendor maxresults 1000',
                    'minorversion' => $this->config()['minor_version'],
                ])
                ->throw()
                ->json();
        } catch (RequestException $e) {
            throw new RuntimeException('QuickBooks vendor query failed: '.$e->getMessage(), 0, $e);
        }

        $vendors = $response['QueryResponse']['Vendor'] ?? [];

        if (! is_array($vendors)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($row) => $this->normalizeVendor($row),
            $vendors
        )));
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{external_id: string, name: string, bank_account: ?string, routing_number: ?string}|null
     */
    private function normalizeVendor(mixed $row): ?array
    {
        if (! is_array($row) || ! isset($row['Id'], $row['DisplayName'])) {
            return null;
        }

        $bankAccount = $row['BankAccountNumber'] ?? null;
        $routingNumber = $row['RoutingNumber'] ?? null;

        return [
            'external_id' => (string) $row['Id'],
            'name' => (string) $row['DisplayName'],
            'bank_account' => is_string($bankAccount) ? $bankAccount : null,
            'routing_number' => is_string($routingNumber) ? $routingNumber : null,
        ];
    }
}
