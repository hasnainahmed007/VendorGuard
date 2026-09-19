<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use App\Services\LocationResolver;
use App\Support\DeviceDetector;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;

class LogFailedLogin
{
    public function __construct(private Request $request, private LocationResolver $locations) {}

    public function handle(Failed $event): void
    {
        $credentials = $event->credentials;

        LoginActivity::create([
            'user_id' => $event->user?->getAuthIdentifier(),
            'email' => is_array($credentials) ? (string) ($credentials['email'] ?? '') : null,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'device' => DeviceDetector::detect($this->request->userAgent()),
            'location' => $this->locations->resolve($this->request->ip()),
            'result' => 'failed',
        ]);
    }
}
