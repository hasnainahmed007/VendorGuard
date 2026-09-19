<?php

namespace App\Http\Responses;

use App\Support\CentralAccess;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * Web registration creates a bare user with no tenant; route by role
     * exactly like login so tenant users never land on the central panel.
     */
    public function toResponse($request): Response
    {
        $user = Auth::user();

        if (CentralAccess::isPrivileged($user)) {
            return redirect()->intended(config('fortify.home', '/superadmin/dashboard'));
        }

        return LoginResponse::tenantRedirect($request);
    }
}
