<?php

namespace App\Services\Email;

use App\Models\Integration;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GmailClient
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const API_URL = 'https://gmail.googleapis.com/gmail/v1';

    /**
     * @return array<string, mixed>
     */
    private function config(): array
    {
        return config('services.gmail');
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
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);
    }

    /**
     * @return array{access_token: string, refresh_token?: string, expires_in?: int}
     */
    public function exchangeCode(string $code): array
    {
        $config = $this->config();

        try {
            $response = Http::asForm()->post(self::TOKEN_URL, [
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $config['redirect'],
            ])->throw()->json();
        } catch (RequestException $e) {
            throw new RuntimeException('Gmail token exchange failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response) || ! isset($response['access_token'])) {
            throw new RuntimeException('Gmail token exchange returned an unexpected response.');
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
            $response = Http::asForm()->post(self::TOKEN_URL, [
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ])->throw()->json();
        } catch (RequestException $e) {
            throw new RuntimeException('Gmail token refresh failed: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($response) || ! isset($response['access_token'])) {
            throw new RuntimeException('Gmail token refresh returned an unexpected response.');
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

    public function profileEmail(Integration $integration): ?string
    {
        $integration = $this->ensureFreshToken($integration->fresh() ?? $integration);

        $response = Http::withToken($integration->access_token)
            ->acceptJson()
            ->get(self::API_URL.'/users/me/profile')
            ->throw()
            ->json();

        return is_string($response['emailAddress'] ?? null) ? $response['emailAddress'] : null;
    }

    /**
     * @return array<int, string> Gmail message ids, newest first.
     */
    public function listMessages(Integration $integration, string $query = 'newer_than:1d', int $max = 20): array
    {
        $integration = $this->ensureFreshToken($integration->fresh() ?? $integration);

        $response = Http::withToken($integration->access_token)
            ->acceptJson()
            ->get(self::API_URL.'/users/me/messages', [
                'q' => $query,
                'maxResults' => $max,
            ])
            ->throw()
            ->json();

        $messages = $response['messages'] ?? [];

        if (! is_array($messages)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($row) => is_array($row) && isset($row['id']) ? (string) $row['id'] : null,
            $messages
        )));
    }

    /**
     * @return array{external_id: string, subject: string, body: string, from: string}
     */
    public function getMessage(Integration $integration, string $gmailId): array
    {
        $integration = $this->ensureFreshToken($integration->fresh() ?? $integration);

        $response = Http::withToken($integration->access_token)
            ->acceptJson()
            ->get(self::API_URL."/users/me/messages/{$gmailId}", ['format' => 'full'])
            ->throw()
            ->json();

        $headers = [];

        foreach ($response['payload']['headers'] ?? [] as $header) {
            if (isset($header['name'], $header['value'])) {
                $headers[strtolower($header['name'])] = $header['value'];
            }
        }

        return [
            'external_id' => $gmailId,
            'subject' => (string) ($headers['subject'] ?? ''),
            'body' => $this->extractText($response['payload'] ?? []) ?: (string) ($response['snippet'] ?? ''),
            'from' => (string) ($headers['from'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function extractText(array $payload): string
    {
        $mime = $payload['mimeType'] ?? '';
        $data = $payload['body']['data'] ?? null;

        if ($mime === 'text/plain' && is_string($data)) {
            return $this->decodeBody($data);
        }

        foreach ($payload['parts'] ?? [] as $part) {
            if (! is_array($part)) {
                continue;
            }

            $text = $this->extractText($part);

            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }

    private function decodeBody(string $data): string
    {
        return (string) base64_decode(strtr($data, '-_', '+/'));
    }
}
