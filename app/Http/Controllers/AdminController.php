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

        $user = auth()->user();
        if ($user && $user->isSocialAid()) {
            $applicationModels = [
                'orphan-care' => \App\Models\OrphanCareApplication::class,
                'differently-abled' => \App\Models\DifferentlyAbledApplication::class,
                'family-aid' => \App\Models\FamilyAidApplication::class,
            ];
        }

        $applicationsCount = 0;
        $todayCount = 0;
        $approvedCount = 0;
        $pendingCount = 0;
        $underReviewCount = 0;
        $rejectedCount = 0;

        $recentList = [];

        foreach ($applicationModels as $slug => $model) {
            $applicationsCount += $model::count();
            $todayCount += $model::whereDate('created_at', now()->today())->count();
            $approvedCount += $model::where('status', 'Approved')->count();
            $pendingCount += $model::where('status', 'Pending')->count();
            $underReviewCount += $model::whereIn('status', ['Under Review', 'In Review', 'Processing'])->count();
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

        // Build Multi-Period Chart Trend Datasets for Applications Overview
        // 1. This Month (Days of current month, e.g. May 1, May 7, May 13...)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daysInMonth = now()->daysInMonth;
        
        $thisMonthLabels = [];
        $thisMonthData = [];
        // Sample 6-7 intervals across the month if daysInMonth > 7, or all days if <= 7
        $step = max(1, (int)floor($daysInMonth / 5));
        for ($day = 1; $day <= $daysInMonth; $day += ($day + $step > $daysInMonth && $day != $daysInMonth ? ($daysInMonth - $day) : $step)) {
            $date = now()->startOfMonth()->addDays($day - 1);
            $thisMonthLabels[] = $date->format('M j');
            $sum = 0;
            foreach ($applicationModels as $model) {
                $sum += $model::whereDate('created_at', '<=', $date->endOfDay())->count();
            }
            $thisMonthData[] = $sum;
            if ($day == $daysInMonth) break;
        }

        // 2. Last 7 Days
        $last7Labels = [];
        $last7Data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7Labels[] = $date->format('M j');
            $sum = 0;
            foreach ($applicationModels as $model) {
                $sum += $model::whereDate('created_at', '<=', $date->endOfDay())->count();
            }
            $last7Data[] = $sum;
        }

        // 3. Last 30 Days (6 data points)
        $last30Labels = [];
        $last30Data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subDays($i * 5);
            $last30Labels[] = $date->format('M j');
            $sum = 0;
            foreach ($applicationModels as $model) {
                $sum += $model::whereDate('created_at', '<=', $date->endOfDay())->count();
            }
            $last30Data[] = $sum;
        }

        // 4. This Year (Months Jan - Dec)
        $thisYearLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $thisYearData = [];
        $currentMonthIndex = (int)now()->format('n');
        for ($m = 1; $m <= 12; $m++) {
            if ($m > $currentMonthIndex) {
                $thisYearData[] = 0;
            } else {
                $date = now()->startOfYear()->addMonths($m - 1)->endOfMonth();
                $sum = 0;
                foreach ($applicationModels as $model) {
                    $sum += $model::whereDate('created_at', '<=', $date)->count();
                }
                $thisYearData[] = $sum;
            }
        }

        $chartPeriodData = [
            'this_month' => ['labels' => $thisMonthLabels, 'data' => $thisMonthData],
            'last_7_days' => ['labels' => $last7Labels, 'data' => $last7Data],
            'last_30_days' => ['labels' => $last30Labels, 'data' => $last30Data],
            'this_year' => ['labels' => $thisYearLabels, 'data' => $thisYearData],
        ];

        // Legacy compatibility
        $chartLabels = $thisMonthLabels;
        $chartAllData = $thisMonthData;

        $viewData = compact(
            'userCount', 
            'donorsCount', 
            'applicationsCount', 
            'todayCount', 
            'approvedCount', 
            'pendingCount', 
            'underReviewCount', 
            'rejectedCount', 
            'recentApplications', 
            'chartLabels', 
            'chartAllData',
            'chartPeriodData'
        );

        if ($user->isReception()) {
            return view('dashboard.reception', $viewData);
        }
        if ($user->isSocialAid()) {
            return view('dashboard.social_aid', $viewData);
        }
        if ($user->isSuperAdmin()) {
            return view('dashboard.admin', $viewData);
        }
        if ($user->isCoo()) {
            return view('dashboard.coo', $viewData);
        }
        if ($user->isPm()) {
            return view('dashboard.project_manager', $viewData);
        }
        if ($user->isHod()) {
            return view('dashboard.hod', $viewData);
        }

        return view('dashboard.others', $viewData);
    }
}
