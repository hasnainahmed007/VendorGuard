<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\SyncGmailMessages;
use App\Jobs\SyncQuickBooksVendors;
use App\Jobs\SyncXeroContacts;
use App\Models\Integration;
use App\Models\Tenant;
use App\Services\Email\GmailClient;
use App\Services\QuickBooks\QuickBooksClient;
use App\Services\Xero\XeroClient;
use App\Support\TenantAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class IntegrationController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Integration::class);

        return view('tenant.integrations.index', [
            'integrations' => Integration::orderBy('provider')->get(),
            'quickbooksConfigured' => app(QuickBooksClient::class)->isConfigured(),
            'xeroConfigured' => app(XeroClient::class)->isConfigured(),
        ]);
    }

    public function connect(Request $request, string $provider): RedirectResponse
    {
        Gate::authorize('create', Integration::class);

        if (! in_array($provider, ['quickbooks', 'gmail', 'xero'], true)) {
            return redirect()->route('tenant.tenants.index')
                ->withErrors(['provider' => ucfirst($provider).' connections are not available yet.']);
        }

        if ($provider === 'gmail') {
            return $this->connectGmail($request);
        }

        if ($provider === 'xero') {
            return $this->connectXero($request);
        }

        $client = app(QuickBooksClient::class);

        if (! $client->isConfigured()) {
            return redirect()->to(route('tenant.integrations.index'))
                ->withErrors(['quickbooks' => 'QuickBooks credentials are not configured. Set QB_CLIENT_ID, QB_CLIENT_SECRET and QB_REDIRECT_URI.']);
        }

        $state = Str::random(40);
        $request->session()->put('qb_oauth_state', [
            'state' => $state,
            'tenant_id' => tenant()->getTenantKey(),
        ]);

        return redirect()->away($client->authorizationUrl($state));
    }

    private function connectGmail(Request $request): RedirectResponse
    {
        $client = app(GmailClient::class);

        if (! $client->isConfigured()) {
            return redirect()->to(route('tenant.integrations.index'))
                ->withErrors(['gmail' => 'Gmail credentials are not configured. Set GMAIL_CLIENT_ID, GMAIL_CLIENT_SECRET and GMAIL_REDIRECT_URI.']);
        }

        $state = Str::random(40);
        $request->session()->put('gmail_oauth_state', [
            'state' => $state,
            'tenant_id' => tenant()->getTenantKey(),
        ]);

        return redirect()->away($client->authorizationUrl($state));
    }

    private function connectXero(Request $request): RedirectResponse
    {
        $client = app(XeroClient::class);

        if (! $client->isConfigured()) {
            return redirect()->to(route('tenant.integrations.index'))
                ->withErrors(['xero' => 'Xero credentials are not configured. Set XERO_CLIENT_ID, XERO_CLIENT_SECRET and XERO_REDIRECT_URI.']);
        }

        $state = Str::random(40);
        $request->session()->put('xero_oauth_state', [
            'state' => $state,
            'tenant_id' => tenant()->getTenantKey(),
        ]);

        return redirect()->away($client->authorizationUrl($state));
    }

    /**
     * OAuth callbacks run outside the tenant middleware: providers cannot
     * send our ?tenant= identifier back. The tenant travels inside the
     * session-bound OAuth state instead, and tenancy is initialized here
     * after the state is verified.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        if (! in_array($provider, ['quickbooks', 'gmail', 'xero'], true)) {
            return redirect()->route('tenant.tenants.index')
                ->withErrors(['provider' => ucfirst($provider).' connections are not available yet.']);
        }

        $input = $request->validate([
            'code' => ['required', 'string'],
            'realmId' => ['nullable', 'string', 'max:255'],
            'state' => ['required', 'string'],
        ]);

        $saved = $request->session()->pull($this->oauthSessionKey($provider));

        if (! is_array($saved)
            || ($saved['state'] ?? null) !== $input['state']
            || ! isset($saved['tenant_id'])) {
            return redirect()->route('tenant.tenants.index')
                ->withErrors(['state' => 'OAuth session expired. Please try connecting again.']);
        }

        $tenant = Tenant::find($saved['tenant_id']);

        if ($tenant === null || ! TenantAccess::canAccess($request->user(), $tenant->getKey())) {
            abort(403, 'Forbidden for this tenant.');
        }

        tenancy()->initialize($tenant);
        $request->session()->put('tenant_id', $tenant->getKey());

        Gate::authorize('create', Integration::class);

        if ($provider === 'gmail') {
            return $this->callbackGmail($request, $tenant->getKey());
        }

        if ($provider === 'xero') {
            return $this->callbackXero($input['code'], $tenant->getKey());
        }

        return $this->callbackQuickBooks($input['code'], $input['realmId'], $tenant->getKey());
    }

    private function oauthSessionKey(string $provider): string
    {
        return match ($provider) {
            'gmail' => 'gmail_oauth_state',
            'xero' => 'xero_oauth_state',
            default => 'qb_oauth_state',
        };
    }

    /**
     * One login may authorize several Xero organisations: create one
     * integration row per connection until the accounting-org cap is hit.
     */
    private function callbackXero(string $code, string $tenantId): RedirectResponse
    {
        $client = app(XeroClient::class);

        $tokens = $client->exchangeCode($code);

        $connections = $client->getConnections($tokens['access_token']);

        if ($connections === []) {
            return redirect()->to(route('tenant.integrations.index'))
                ->withErrors(['provider' => 'No Xero organisations were authorized.']);
        }

        $connected = 0;

        foreach ($connections as $connection) {
            $accountingCount = Integration::where('tenant_id', $tenantId)
                ->whereIn('provider', Integration::ACCOUNTING_PROVIDERS)
                ->count();

            if ($accountingCount >= 2) {
                break;
            }

            Integration::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'provider' => 'xero',
                    'external_account_id' => $connection['tenant_id'],
                ],
                [
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'] ?? null,
                    'expires_at' => isset($tokens['expires_in'])
                        ? now()->addSeconds((int) $tokens['expires_in'])
                        : null,
                    'status' => 'connected',
                ]
            );

            $connected++;
        }

        if ($connected === 0) {
            return redirect()->to(route('tenant.integrations.index'))
                ->withErrors(['provider' => 'The MVP plan allows up to 2 connected accounting orgs.']);
        }

        SyncXeroContacts::dispatch();

        return redirect()
            ->to(route('tenant.vendors.index'))
            ->with('success', "Xero connected ({$connected} organisation(s)). Contacts are syncing in the background.");
    }

    private function callbackQuickBooks(string $code, ?string $realmId, string $tenantId): RedirectResponse
    {
        $client = app(QuickBooksClient::class);

        if ($realmId === null || $realmId === '') {
            return redirect()->to(route('tenant.integrations.index'))
                ->withErrors(['realmId' => 'QuickBooks did not return a company (realmId).']);
        }

        $accountingCount = Integration::whereIn('provider', Integration::ACCOUNTING_PROVIDERS)->count();

        if ($accountingCount >= 2) {
            return redirect()->to(route('tenant.integrations.index'))
                ->withErrors(['provider' => 'The MVP plan allows up to 2 connected accounting orgs.']);
        }

        $tokens = $client->exchangeCode($code);

        Integration::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'provider' => 'quickbooks',
                'external_account_id' => $realmId,
            ],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'] ?? null,
                'expires_at' => isset($tokens['expires_in'])
                    ? now()->addSeconds((int) $tokens['expires_in'])
                    : null,
                'status' => 'connected',
            ]
        );

        SyncQuickBooksVendors::dispatch();

        return redirect()
            ->to(route('tenant.vendors.index'))
            ->with('success', 'QuickBooks connected. Vendors are syncing in the background.');
    }

    private function callbackGmail(Request $request, string $tenantId): RedirectResponse
    {
        $client = app(GmailClient::class);

        $code = (string) $request->input('code');

        $tokens = $client->exchangeCode($code);

        $integration = Integration::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'provider' => 'gmail',
                'external_account_id' => 'pending',
            ],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'] ?? null,
                'expires_at' => isset($tokens['expires_in'])
                    ? now()->addSeconds((int) $tokens['expires_in'])
                    : null,
                'status' => 'connected',
            ]
        );

        $email = $client->profileEmail($integration);

        if ($email !== null) {
            $integration->update(['external_account_id' => $email]);
        }

        SyncGmailMessages::dispatch();

        return redirect()
            ->to(route('tenant.integrations.index'))
            ->with('success', 'Gmail connected. Inboxes are syncing in the background.');
    }

    public function destroy(Integration $integration): RedirectResponse
    {
        $this->ensureTenantModel($integration);
        Gate::authorize('delete', $integration);

        $integration->delete();

        return redirect()
            ->to(route('tenant.integrations.index'))
            ->with('success', 'Integration disconnected.');
    }
}
