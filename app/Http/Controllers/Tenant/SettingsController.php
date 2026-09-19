<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationSettingsRequest;
use App\Models\NotificationSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('tenant.settings.index', [
            'settings' => NotificationSetting::for(tenant()->getTenantKey()),
        ]);
    }

    public function updateNotifications(UpdateNotificationSettingsRequest $request): RedirectResponse
    {
        $settings = NotificationSetting::for(tenant()->getTenantKey());
        $settings->update($request->validated());

        return redirect()
            ->route('tenant.settings.index')
            ->with('success', 'Notification preferences saved.');
    }
}
