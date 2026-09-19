<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditLogActivityStore
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('get')) {
            return $response;
        }

        $user = $request->user();

        if ($user === null || $response->getStatusCode() >= 400) {
            return $response;
        }

        if ($request->session()->has('errors')) {
            return $response;
        }

        $route = $request->route();

        if ($route === null) {
            return $response;
        }

        AuditLog::create([
            'user_id' => $user->getAuthIdentifier(),
            'action' => $this->actionLabel((string) $route->getName()),
            'target' => $this->targetLabel($request),
            'method' => $request->getMethod(),
            'path' => ltrim($request->path(), '/'),
            'ip_address' => $request->ip(),
        ]);

        return $response;
    }

    private function actionLabel(string $routeName): string
    {
        $segments = explode('.', $routeName);
        $verb = (string) end($segments);
        $resource = (string) ($segments[count($segments) - 2] ?? 'record');

        if ($resource === 'send') {
            return 'Sent notification';
        }

        $subject = (string) Str::of($resource)->singular()->replace('-', ' ');

        return match ($verb) {
            'store' => "Created {$subject}",
            'update' => "Updated {$subject}",
            'destroy' => "Deleted {$subject}",
            'assign' => "Assigned {$subject}",
            'remove' => "Removed {$subject}",
            default => Str::headline("{$verb} {$subject}"),
        };
    }

    private function targetLabel(Request $request): ?string
    {
        foreach (['name', 'title', 'email', 'subject'] as $key) {
            $value = $request->input($key);

            if (is_string($value) && $value !== '') {
                return Str::limit($value, 80);
            }
        }

        foreach ($request->route()?->parameters() ?? [] as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'getKey')) {
                return '#'.$parameter->getKey();
            }

            if (is_scalar($parameter)) {
                return '#'.$parameter;
            }
        }

        return null;
    }
}
