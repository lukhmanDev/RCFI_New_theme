<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CooStaffDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user || (!$user->isSuperAdmin() && !$user->isCoo() && !in_array($user->role, ['super_admin', 'coo', 'hod']))) {
            abort(403, 'Unauthorized access to COO Staff Operations Dashboard.');
        }

        return view('admin.coo_staff_dashboard');
    }
}
