<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:subscriptions.read')->only('index');
    }

    public function index(): View
    {
        return view('superadmin.subscriptions.index');
    }
}
