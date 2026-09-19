<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Home of every tenant-scoped web route. Tenancy resolves from the user's
| own session (workspace switcher, home-tenant fallback) via the
| tenant.session middleware — no identifier travels in URLs. The
| BelongsToTenant scope on every model then restricts all queries to
| the resolved tenant. The API (routes/api.php) keeps using the
| tenancy package's RequestData identification (X-Tenant header).
|
| These routes are loaded by TenancyServiceProvider on every domain.
|
*/

Route::middleware(['web', 'auth'])->group(function () {
    // Workspace selection needs no tenancy: this is how a user picks (or
    // accepts) a tenant in the first place.
    Route::get('/app/tenants', [Tenant\SwitcherController::class, 'index'])->name('tenant.tenants.index');
    Route::post('/app/tenants/switch', [Tenant\SwitcherController::class, 'store'])->name('tenant.tenants.switch');

    // OAuth providers redirect back without session context guarantees,
    // so callbacks resolve the tenant from the session-bound OAuth state.
    Route::get('/app/integrations/{provider}/callback', [Tenant\IntegrationController::class, 'callback'])->name('tenant.integrations.callback');

    // Invitation links are opened by users who do not have tenant access
    // yet — that is the point — so acceptance lives outside tenancy.
    Route::get('/app/invitations/accept', [Tenant\InvitationController::class, 'accept'])->name('tenant.invitations.accept');
});

Route::middleware(['web', 'auth', 'tenant.session', 'tenant.access', 'tenant.initialized'])->group(function () {
    Route::get('/app/incidents', [Tenant\IncidentController::class, 'index'])->name('tenant.incidents.index');
    Route::get('/app/incidents/{incident}', [Tenant\IncidentController::class, 'show'])->name('tenant.incidents.show');
    Route::post('/app/incidents/{incident}/transition', [Tenant\IncidentController::class, 'transition'])->name('tenant.incidents.transition');

    Route::get('/app/vendors', [Tenant\VendorController::class, 'index'])->name('tenant.vendors.index');
    Route::get('/app/vendors/create', [Tenant\VendorController::class, 'create'])->name('tenant.vendors.create');
    Route::post('/app/vendors', [Tenant\VendorController::class, 'store'])->name('tenant.vendors.store');
    Route::post('/app/vendors/{vendor}/changes', [Tenant\VendorController::class, 'recordChange'])->name('tenant.vendors.changes.store');
    Route::delete('/app/vendors/{vendor}', [Tenant\VendorController::class, 'destroy'])->name('tenant.vendors.destroy');
    Route::get('/app/vendors/{vendor}/verify', [Tenant\VendorController::class, 'verifyForm'])->name('tenant.vendors.verify');
    Route::post('/app/vendors/{vendor}/verify', [Tenant\VendorController::class, 'verify'])->name('tenant.vendors.verify.store');

    Route::get('/app/audit', [Tenant\AuditController::class, 'index'])->name('tenant.audit.index');

    Route::get('/app/billing', [Tenant\BillingController::class, 'index'])->name('tenant.billing.index');
    Route::post('/app/billing/checkout', [Tenant\BillingController::class, 'checkout'])->name('tenant.billing.checkout');

    Route::get('/app/integrations', [Tenant\IntegrationController::class, 'index'])->name('tenant.integrations.index');
    Route::get('/app/integrations/{provider}/connect', [Tenant\IntegrationController::class, 'connect'])->name('tenant.integrations.connect');
    Route::delete('/app/integrations/{integration}', [Tenant\IntegrationController::class, 'destroy'])->name('tenant.integrations.destroy');

    Route::get('/app/team', [Tenant\InvitationController::class, 'index'])->name('tenant.team.index');
    Route::post('/app/team/invitations', [Tenant\InvitationController::class, 'store'])->name('tenant.team.invitations.store');
    Route::delete('/app/team/invitations/{invitation}', [Tenant\InvitationController::class, 'revoke'])->name('tenant.team.invitations.revoke');
    Route::delete('/app/team/members/{access}', [Tenant\InvitationController::class, 'removeMember'])->name('tenant.team.members.remove');

    Route::get('/app/settings', [Tenant\SettingsController::class, 'index'])->name('tenant.settings.index');
    Route::put('/app/settings/notifications', [Tenant\SettingsController::class, 'updateNotifications'])->name('tenant.settings.notifications.update');
});
