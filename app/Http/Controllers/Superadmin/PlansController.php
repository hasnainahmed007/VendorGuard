<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class PlansController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:plans.read')->only('index');
        $this->middleware('permission:plans.create')->only('create');
        $this->middleware('permission:plans.edit')->only('edit');
    }

    public function index(): View
    {
        return view('superadmin.plans.index');
    }

    public function create(): View
    {
        return view('superadmin.plans.create');
    }

    public function edit(string $plan): View
    {
        return view('superadmin.plans.edit', ['plan' => $plan]);
    }
}
