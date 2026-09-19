<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LoginActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:login-activities.read')->only('index');
    }

    public function index(Request $request): View
    {
        $results = LoginActivity::RESULTS;

        $search = $request->string('search')->toString();
        $result = $request->string('result')->toString();
        $device = $request->string('device')->toString();

        $activities = LoginActivity::with(['user:id,name'])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('email', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"))))
            ->when(in_array($result, $results, true), fn ($query) => $query->where('result', $result))
            ->when($device !== '', fn ($query) => $query->where('device', $device))
            ->latest()
            ->orderByDesc('id')
            ->paginate()
            ->withQueryString();

        $devices = LoginActivity::query()
            ->select('device')
            ->whereNotNull('device')
            ->distinct()
            ->orderBy('device')
            ->pluck('device');

        return view('superadmin.login-activities.index', compact('activities', 'results', 'devices', 'search', 'result', 'device'));
    }
}
