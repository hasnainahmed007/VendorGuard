<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use App\Services\Billing\BillingService;
use App\Services\Billing\StripeClient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class BillingController extends Controller
{
    public function index(BillingService $billing): View
    {
        Gate::authorize('viewAny', Integration::class);

        $tenant = tenant();

        return view('tenant.billing.index', [
            'tenant' => $tenant,
            'isActive' => $billing->isActive($tenant),
            'stripeConfigured' => app(StripeClient::class)->isConfigured(),
            'price' => config('services.stripe.price_id'),
        ]);
    }

    public function checkout(StripeClient $stripe): RedirectResponse
    {
        Gate::authorize('create', Integration::class);

        if (! $stripe->isConfigured() || config('services.stripe.price_id') === null) {
            return redirect()->to(route('tenant.billing.index'))
                ->withErrors(['stripe' => 'Billing is not configured yet.']);
        }

        $tenant = tenant();

        $session = $stripe->createCheckoutSession(
            (string) ($tenant->email ?? ''),
            (string) config('services.stripe.price_id'),
            route('tenant.billing.index', ['checkout' => 'success']),
            route('tenant.billing.index', ['checkout' => 'cancelled']),
        );

        return redirect()->away($session['url'], 303);
    }
}
