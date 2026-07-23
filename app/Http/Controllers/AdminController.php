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
        
        $applicationModels = [
            'education-center' => \App\Models\EducationCenterApplication::class,
            'cultural-center' => \App\Models\CulturalCenterApplication::class,
            'hospital-or-clinics' => \App\Models\HospitalClinicApplication::class,
            'shops-and-others' => \App\Models\ShopOtherApplication::class,
            'house' => \App\Models\HouseApplication::class,
            'drinking-water-group-level' => \App\Models\DrinkingWaterGroupApplication::class,
            'drinking-water-individual-level' => \App\Models\DrinkingWaterIndividualApplication::class,
            'orphan-care' => \App\Models\OrphanCareApplication::class,
            'differently-abled' => \App\Models\DifferentlyAbledApplication::class,
            'family-aid' => \App\Models\FamilyAidApplication::class,
            'general' => \App\Models\GeneralApplication::class,
        ];

        $applicationsCount = 0;
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;

        $recentList = [];

        foreach ($applicationModels as $slug => $model) {
            $applicationsCount += $model::count();
            $approvedCount += $model::where('status', 'Approved')->count();
            $pendingCount += $model::where('status', 'Pending')->count();
            $rejectedCount += $model::where('status', 'Rejected')->count();

            // Fetch recent 3 from each
            $recentApps = $model::orderBy('created_at', 'desc')->take(3)->get();
            foreach ($recentApps as $app) {
                $recentList[] = [
                    'id' => $app->id,
                    'applicant_name' => $app->applicant_name,
                    'status' => $app->status,
                    'created_at' => $app->created_at,
                    'category' => $slug,
                    'category_name' => str_replace('-', ' ', ucwords($slug, '-')),
                ];
            }
        }

        // Sort by created_at desc
        usort($recentList, function($a, $b) {
            if (!$a['created_at'] && !$b['created_at']) return 0;
            if (!$a['created_at']) return 1;
            if (!$b['created_at']) return -1;
            return strcmp($b['created_at']->toDateTimeString(), $a['created_at']->toDateTimeString());
        });

        $recentApplications = array_slice($recentList, 0, 3);

        $user = auth()->user();
        if ($user->isReception()) {
            return view('dashboard.reception', compact('applicationsCount', 'pendingCount', 'recentApplications'));
        }
        if ($user->isSuperAdmin()) {
            return view('dashboard.admin', compact('userCount', 'donorsCount', 'applicationsCount', 'approvedCount', 'pendingCount', 'rejectedCount', 'recentApplications'));
        }
        if ($user->isCoo()) {
            return view('dashboard.coo', compact('donorsCount', 'applicationsCount', 'approvedCount', 'pendingCount', 'rejectedCount'));
        }
        if ($user->isPm()) {
            return view('dashboard.project_manager', compact('donorsCount', 'applicationsCount', 'approvedCount', 'pendingCount', 'rejectedCount'));
        }
        if ($user->isHod()) {
            return view('dashboard.hod', compact('donorsCount', 'applicationsCount', 'approvedCount', 'pendingCount', 'rejectedCount'));
        }

        return view('dashboard.others', compact('donorsCount', 'applicationsCount', 'approvedCount', 'pendingCount', 'rejectedCount'));
    }
}
