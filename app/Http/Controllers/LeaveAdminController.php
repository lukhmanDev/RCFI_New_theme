<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveAdminController extends Controller
{
    public function index()
    {
        if (!auth()->user() || (!auth()->user()->hasAdminAccess() && !in_array(auth()->user()->role, ['super_admin', 'coo', 'hod', 'project_manager', 'social_aid']))) {
            abort(403, 'Unauthorized access to Leave Admin Dashboard.');
        }

        return view('admin.leave_admin_portal');
    }
}
