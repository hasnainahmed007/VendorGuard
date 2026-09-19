<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Jobs\SendTenantNotification;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SendNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:notifications.read')->only('index');
        $this->middleware('permission:notifications.create')->only('store');
    }

    public function index(): View
    {
        $templates = NotificationTemplate::active()
            ->latest()
            ->orderByDesc('id')
            ->get();

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->limit(500)
            ->get();

        return view('superadmin.notifications.send.index', compact('templates', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'audience' => ['required', 'string', 'in:all,specific'],
            'user_id' => ['required_if:audience,specific', 'nullable', 'integer', 'exists:users,id'],
            'template_id' => ['nullable', 'integer', 'exists:notification_templates,id'],
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:160'],
            'channel' => ['required', 'string', 'in:'.implode(',', NotificationLog::CHANNELS)],
        ]);

        $recipients = $validated['audience'] === 'specific'
            ? User::where('id', $validated['user_id'])->get()
            : User::query()->orderBy('id')->get();

        if ($recipients->isEmpty()) {
            return back()->withErrors(['audience' => 'No recipients found for the selected audience.'])->withInput();
        }

        foreach ($recipients as $recipient) {
            $log = NotificationLog::create([
                'notification_template_id' => $validated['template_id'] ?? null,
                'user_id' => $recipient->id,
                'audience' => $validated['audience'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'channel' => $validated['channel'],
                'status' => 'queued',
            ]);

            SendTenantNotification::dispatch($log->id)->afterCommit();
        }

        return redirect()->route('superadmin.notifications.logs.index')
            ->with('success', "Notification queued for {$recipients->count()} recipient(s).");
    }
}
