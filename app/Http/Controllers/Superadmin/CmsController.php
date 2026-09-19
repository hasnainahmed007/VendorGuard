<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cms.read')->only('index');
    }

    public function index(): View
    {
        return view('superadmin.cms.index');
    }
}
