<?php

use App\Http\Middleware\AuditLogActivityStore;
use App\Http\Middleware\EnsureTenantAccess;
use App\Http\Middleware\EnsureTenantInitialized;
use App\Http\Middleware\InitializeTenancyBySession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'audit_log' => AuditLogActivityStore::class,
            'tenant.access' => EnsureTenantAccess::class,
            'tenant.session' => InitializeTenancyBySession::class,
            'tenant.initialized' => EnsureTenantInitialized::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (TenantCouldNotBeIdentifiedException $exception, Request $request) {
            // Tenancy resolves from the user's session on web; an
            // unresolvable tenant means a broken or foreign context.
            return response()->json(['message' => 'Tenant could not be identified.'], 404);
        });
    })->create();
