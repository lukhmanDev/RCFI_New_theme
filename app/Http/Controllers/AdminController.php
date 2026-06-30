<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Donor;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $donorsCount = Donor::count();
        $applicationsCount = \App\Models\EducationCenterApplication::count()
            + \App\Models\CulturalCenterApplication::count()
            + \App\Models\HospitalClinicApplication::count()
            + \App\Models\ShopOtherApplication::count()
            + \App\Models\HouseApplication::count()
            + \App\Models\DrinkingWaterGroupApplication::count()
            + \App\Models\DrinkingWaterIndividualApplication::count()
            + \App\Models\OrphanCareApplication::count()
            + \App\Models\DifferentlyAbledApplication::count()
            + \App\Models\FamilyAidApplication::count()
            + \App\Models\GeneralApplication::count();

        $role = auth()->user()->role;
        
        switch ($role) {
            case 1:
                return view('dashboard.admin', compact('userCount', 'donorsCount', 'applicationsCount'));
            case 2:
                return view('dashboard.coo', compact('donorsCount', 'applicationsCount'));
            case 3:
                return view('dashboard.project_manager', compact('donorsCount', 'applicationsCount'));
            case 4:
                return view('dashboard.hod', compact('donorsCount', 'applicationsCount'));
            default:
                return view('dashboard.others', compact('donorsCount', 'applicationsCount'));
        }
    }
}
