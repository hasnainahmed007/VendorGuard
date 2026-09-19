<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use App\Services\LocationResolver;
use App\Support\DeviceDetector;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    public function __construct(private Request $request, private LocationResolver $locations) {}

    public function handle(Login $event): void
    {
        LoginActivity::create([
            'user_id' => $event->user->getAuthIdentifier(),
            'email' => $event->user->getEmailForPasswordReset(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'device' => DeviceDetector::detect($this->request->userAgent()),
            'location' => $this->locations->resolve($this->request->ip()),
            'result' => 'success',
        ]);
    }
}
