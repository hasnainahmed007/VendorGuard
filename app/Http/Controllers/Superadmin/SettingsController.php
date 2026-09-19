<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.read')->only('index');
    }

    public function index(): View
    {
        return view('superadmin.settings.index');
    }
}
