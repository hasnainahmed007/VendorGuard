<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.read')->only('index');
    }

    public function index(): View
    {
        return view('superadmin.users.index');
    }
}
