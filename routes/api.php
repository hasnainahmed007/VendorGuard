<?php

use App\Http\Controllers\Api\V1\StripeWebhookController;
use Illuminate\Support\Facades\Route;

// Only machine-to-machine routes live here. Everything human-facing is a
// web route (routes/tenant.php, routes/web.php). Stripe authenticates via
// webhook signature, so this endpoint carries no session or token auth.
Route::prefix('v1')->as('api.v1.')->group(function () {
    Route::post('/billing/stripe/webhook', StripeWebhookController::class)->name('billing.stripe.webhook');
});
