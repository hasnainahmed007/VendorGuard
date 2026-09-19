<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:audit-logs.read')->only('index');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $staffId = $request->integer('staff');
        $action = $request->string('action')->toString();

        $logs = AuditLog::with(['user:id,name'])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('action', 'like', "%{$search}%")
                ->orWhere('target', 'like', "%{$search}%")))
            ->when($staffId > 0, fn ($query) => $query->where('user_id', $staffId))
            ->when($action !== '', fn ($query) => $query->where('action', $action))
            ->latest()
            ->orderByDesc('id')
            ->paginate()
            ->withQueryString();

        $staffs = User::query()
            ->select(['id', 'name'])
            ->whereIn('id', AuditLog::query()->select('user_id')->whereNotNull('user_id')->distinct())
            ->orderBy('name')
            ->get();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('superadmin.audit-logs.index', compact('logs', 'staffs', 'actions', 'search', 'staffId', 'action'));
    }
}
