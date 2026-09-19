<?php

use App\Http\Controllers\Superadmin;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });

        Route::group(['as' => 'superadmin.', 'prefix' => 'superadmin', 'middleware' => ['auth', 'audit_log']], function () {
            Route::resource('dashboard', Superadmin\DashboardController::class)->only(['index']);
            Route::resource('users', Superadmin\UsersController::class)->only(['index']);
            Route::resource('plans', Superadmin\PlansController::class)->only(['index', 'create', 'edit']);
            Route::resource('subscriptions', Superadmin\SubscriptionsController::class)->only(['index']);
            Route::prefix('notifications')->as('notifications.')->group(function () {
                Route::resource('send', Superadmin\SendNotificationController::class)->only(['index', 'store']);
                Route::resource('templates', Superadmin\NotificationTemplateController::class)->except(['show']);
                Route::resource('logs', Superadmin\NotificationLogController::class)->only(['index']);
            });
            Route::resource('cms', Superadmin\CmsController::class)->only(['index']);
            Route::resource('payments', Superadmin\PaymentsController::class)->only(['index']);
            Route::resource('staff', Superadmin\StaffController::class);
            Route::resource('roles', Superadmin\RoleController::class)->except(['show']);
            Route::resource('permissions', Superadmin\PermissionController::class)->only(['index']);
            Route::post('permissions/assign', [Superadmin\PermissionController::class, 'assign'])->name('permissions.assign');
            Route::delete('permissions/remove', [Superadmin\PermissionController::class, 'remove'])->name('permissions.remove');
            Route::prefix('system')->as('system.')->group(function () {
                Route::resource('audit-logs', Superadmin\AuditLogController::class)->only(['index']);
                Route::resource('login-activities', Superadmin\LoginActivityController::class)->only(['index']);
            });
            Route::resource('settings', Superadmin\SettingsController::class)->only(['index']);
            Route::get('/tenant-users/stats', [Superadmin\TenantUserController::class, 'stats'])->middleware('permission:users.read');
            Route::resource('tenant-users', Superadmin\TenantUserController::class)->only(['index'])->middleware('permission:users.read');
        });
    });
}
