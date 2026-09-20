<?php

namespace App\Services\Xero;

use App\Models\Integration;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class XeroClient
{
    private const AUTH_URL = 'https://login.xero.com/identity/connect/authorize';

    private const TOKEN_URL = 'https://identity.xero.com/connect/token';

    private const CONNECTIONS_URL = 'https://api.xero.com/connections';

    private const API_URL = 'https://api.xero.com/api.xro/2.0';

    /**
     * @return array<string, mixed>
     */
    private function config(): array
    {
        return config('services.xero');
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
            throw new RuntimeException('Xero token exchange failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response) || ! isset($response['access_token'])) {
            throw new RuntimeException('Xero token exchange returned an unexpected response.');
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
            throw new RuntimeException('Xero token refresh failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response) || ! isset($response['access_token'])) {
            throw new RuntimeException('Xero token refresh returned an unexpected response.');
        }

        return $response;
    }

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
     * Every Xero organisation the user authorized (unlike QuickBooks'
     * single realmId, one login may grant several tenants).
     *
     * @return array<int, array{tenant_id: string}>
     */
    public function getConnections(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->get(self::CONNECTIONS_URL)
                ->throw()
                ->json();
        } catch (RequestException $e) {
            throw new RuntimeException('Xero connections lookup failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response)) {
            return [];
        }

        $connections = [];

        foreach ($response as $row) {
            if (is_array($row) && isset($row['tenantId']) && is_string($row['tenantId'])) {
                $connections[] = ['tenant_id' => $row['tenantId']];
            }
        }

        return $connections;
    }

    /**
     * Pull contacts for one authorized organisation. Unlike QuickBooks,
     * Xero contacts carry a real BankAccountDetails field.
     *
     * @return array<int, array{external_id: string, name: string, bank_account: ?string, routing_number: ?string}>
     */
    public function listContacts(Integration $integration): array
    {
        $integration = $this->ensureFreshToken($integration->fresh() ?? $integration);

        try {
            $response = Http::withToken($integration->access_token)
                ->acceptJson()
                ->withHeaders(['xero-tenant-id' => (string) $integration->external_account_id])
                ->get(self::API_URL.'/Contacts', ['page' => 1])
                ->throw()
                ->json();
        } catch (RequestException $e) {
            throw new RuntimeException('Xero contacts query failed: '.$e->getMessage(), 0, $e);
        }

        $contacts = $response['Contacts'] ?? [];

        if (! is_array($contacts)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($row) => $this->normalizeContact($row),
            $contacts
        )));
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{external_id: string, name: string, bank_account: ?string, routing_number: ?string}|null
     */
    private function normalizeContact(mixed $row): ?array
    {
        if (! is_array($row) || ! isset($row['ContactID'], $row['Name'])) {
            return null;
        }

        $bankAccount = $row['BankAccountDetails'] ?? null;

        return [
            'external_id' => (string) $row['ContactID'],
            'name' => (string) $row['Name'],
            'bank_account' => is_string($bankAccount) && $bankAccount !== '' ? $bankAccount : null,
            'routing_number' => null,
        ];
    }
}
