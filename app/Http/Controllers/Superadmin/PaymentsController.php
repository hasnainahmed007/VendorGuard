<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payments.read')->only('index');
    }

    public function index(): View
    {
        return view('superadmin.payments.index');
    }
}
