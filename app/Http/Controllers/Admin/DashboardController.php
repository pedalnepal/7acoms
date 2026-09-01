<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbstractSubmission;
use App\Models\Registration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view dashboard');
    }

    public function index()
    {
        return view('admin.dashboard', [
            'totalRegistrations' => Registration::count(),
            'totalAbstracts'     => AbstractSubmission::count(),
        ]);
    }
}
