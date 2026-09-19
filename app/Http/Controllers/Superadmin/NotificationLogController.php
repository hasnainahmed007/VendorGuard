<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:notifications.read')->only('index');
    }

    public function index(Request $request): View
    {
        $statuses = NotificationLog::STATUSES;
        $channels = NotificationLog::CHANNELS;

        $status = $request->string('status')->toString();
        $channel = $request->string('channel')->toString();
        $search = $request->string('search')->toString();

        $logs = NotificationLog::with(['user:id,name', 'template:id,name'])
            ->when(in_array($status, $statuses, true), fn ($query) => $query->where('status', $status))
            ->when(in_array($channel, $channels, true), fn ($query) => $query->where('channel', $channel))
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('title', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%")))
            ->latest()
            ->orderByDesc('id')
            ->paginate()
            ->withQueryString();

        return view('superadmin.notifications.logs.index', compact('logs', 'statuses', 'channels', 'status', 'channel', 'search'));
    }
}
