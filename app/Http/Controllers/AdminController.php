<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function socialAidFundReport(\Illuminate\Http\Request $request)
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Only Super Admin can view the Social Aid Fund Report.');
        }

        // Fetch Orphan Care funds
        $orphanFunds = \App\Models\OrphanCareFund::with(['project', 'project.projectManager', 'project.donor', 'donorModel'])->get()->map(function($f) {
            $app = null;
            if ($f->project && $f->project->application_id) {
                $app = \App\Models\OrphanCareApplication::find($f->project->application_id);
            }
            $donorName = $f->donor ?? $f->agency ?? 'N/A';
            $donorShort = $f->donorModel->short_name ?? null;
            $displayDonor = $f->donorModel ? ($donorShort ? "{$f->donorModel->name} ({$donorShort})" : $f->donorModel->name) : $donorName;

            return [
                'id' => $f->id,
                'category' => 'Orphan Care',
                'category_slug' => 'orphan-care',
                'project_id' => $f->project->project_id ?? 'N/A',
                'agency_project_no' => $f->agency_project_no ?? $f->project->agency_project_no ?? 'N/A',
                'project_db_id' => $f->project->id ?? null,
                'applicant_name' => $app->applicant_name ?? $f->project->name ?? 'N/A',
                'applicant_id' => $app->applicant_id ?? 'N/A',
                'photo' => $app->student_photo ? asset($app->student_photo) : null,
                'donor' => $donorName,
                'agency' => $displayDonor,
                'sponsor' => $f->project->sponsor ?? $displayDonor,
                'theme' => $f->project->theme ?? 'N/A',
                'subtheme' => $f->project->subtheme ?? 'N/A',
                'activity' => $f->project->activity ?? 'N/A',
                'project_spec' => $f->project->project_spec ?? 'N/A',
                'available_budget' => (float)($f->project->available_budget ?? 0),
                'remarks' => $f->project->remarks ?? 'N/A',
                'location' => trim(($app->place ?? '') . ' ' . ($app->district ?? '') . ' ' . ($app->state ?? '')) ?: 'N/A',
                'date' => $f->date ? date('Y-m-d', strtotime($f->date)) : null,
                'formatted_date' => $f->date ? date('d M Y', strtotime($f->date)) : 'N/A',
                'created_date' => $f->created_at ? $f->created_at->format('Y-m-d') : ($f->date ? date('Y-m-d', strtotime($f->date)) : null),
                'formatted_created_date' => $f->created_at ? $f->created_at->format('d M Y') : 'N/A',
                'amount' => (float)$f->amount,
            ];
        });

        // Fetch Differently Abled funds
        $daFunds = \App\Models\DifferentlyAbledFund::with(['project', 'project.projectManager', 'project.donor', 'donorModel'])->get()->map(function($f) {
            $app = null;
            if ($f->project && $f->project->application_id) {
                $app = \App\Models\DifferentlyAbledApplication::find($f->project->application_id);
            }
            $donorName = $f->donor ?? $f->agency ?? 'N/A';
            $donorShort = $f->donorModel->short_name ?? null;
            $displayDonor = $f->donorModel ? ($donorShort ? "{$f->donorModel->name} ({$donorShort})" : $f->donorModel->name) : $donorName;

            return [
                'id' => $f->id,
                'category' => 'Differently Abled',
                'category_slug' => 'differently-abled',
                'project_id' => $f->project->project_id ?? 'N/A',
                'agency_project_no' => $f->agency_project_no ?? $f->project->agency_project_no ?? 'N/A',
                'project_db_id' => $f->project->id ?? null,
                'applicant_name' => $app->applicant_name ?? $f->project->name ?? 'N/A',
                'applicant_id' => $app->applicant_id ?? 'N/A',
                'photo' => $app->student_photo ? asset($app->student_photo) : null,
                'donor' => $donorName,
                'agency' => $displayDonor,
                'sponsor' => $f->project->sponsor ?? $displayDonor,
                'theme' => $f->project->theme ?? 'N/A',
                'subtheme' => $f->project->subtheme ?? 'N/A',
                'activity' => $f->project->activity ?? 'N/A',
                'project_spec' => $f->project->project_spec ?? 'N/A',
                'available_budget' => (float)($f->project->available_budget ?? 0),
                'remarks' => $f->project->remarks ?? 'N/A',
                'location' => trim(($app->place ?? '') . ' ' . ($app->district ?? '') . ' ' . ($app->state ?? '')) ?: 'N/A',
                'date' => $f->date ? date('Y-m-d', strtotime($f->date)) : null,
                'formatted_date' => $f->date ? date('d M Y', strtotime($f->date)) : 'N/A',
                'created_date' => $f->created_at ? $f->created_at->format('Y-m-d') : ($f->date ? date('Y-m-d', strtotime($f->date)) : null),
                'formatted_created_date' => $f->created_at ? $f->created_at->format('d M Y') : 'N/A',
                'amount' => (float)$f->amount,
            ];
        });

        // Fetch Family Aid funds
        $faFunds = \App\Models\FamilyAidFund::with(['project', 'project.projectManager', 'project.donor', 'donorModel'])->get()->map(function($f) {
            $app = null;
            if ($f->project && $f->project->application_id) {
                $app = \App\Models\FamilyAidApplication::find($f->project->application_id);
            }
            $donorName = $f->donor ?? $f->agency ?? 'N/A';
            $donorShort = $f->donorModel->short_name ?? null;
            $displayDonor = $f->donorModel ? ($donorShort ? "{$f->donorModel->name} ({$donorShort})" : $f->donorModel->name) : $donorName;

            return [
                'id' => $f->id,
                'category' => 'Family Aid',
                'category_slug' => 'family-aid',
                'project_id' => $f->project->project_id ?? 'N/A',
                'agency_project_no' => $f->agency_project_no ?? $f->project->agency_project_no ?? 'N/A',
                'project_db_id' => $f->project->id ?? null,
                'applicant_name' => $app->applicant_name ?? $f->project->name ?? 'N/A',
                'applicant_id' => $app->applicant_id ?? 'N/A',
                'photo' => $app->student_photo ? asset($app->student_photo) : null,
                'donor' => $donorName,
                'agency' => $displayDonor,
                'sponsor' => $f->project->sponsor ?? $displayDonor,
                'theme' => $f->project->theme ?? 'N/A',
                'subtheme' => $f->project->subtheme ?? 'N/A',
                'activity' => $f->project->activity ?? 'N/A',
                'project_spec' => $f->project->project_spec ?? 'N/A',
                'available_budget' => (float)($f->project->available_budget ?? 0),
                'remarks' => $f->project->remarks ?? 'N/A',
                'location' => trim(($app->place ?? '') . ' ' . ($app->district ?? '') . ' ' . ($app->state ?? '')) ?: 'N/A',
                'date' => $f->date ? date('Y-m-d', strtotime($f->date)) : null,
                'formatted_date' => $f->date ? date('d M Y', strtotime($f->date)) : 'N/A',
                'created_date' => $f->created_at ? $f->created_at->format('Y-m-d') : ($f->date ? date('Y-m-d', strtotime($f->date)) : null),
                'formatted_created_date' => $f->created_at ? $f->created_at->format('d M Y') : 'N/A',
                'amount' => (float)$f->amount,
            ];
        });

        $allFunds = $orphanFunds->concat($daFunds)->concat($faFunds)->sortByDesc(function($item) {
            return $item['date'] ?? '1970-01-01';
        })->values();

        // Handle CSV Export
        if ($request->input('export') === 'csv') {
            $fileName = 'social_aid_fund_report_' . date('Y_m_d_H_i_s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function() use ($allFunds) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['#', 'Date', 'Category', 'Agency Project No', 'Project ID', 'Beneficiary / Applicant', 'Agency / Sponsor', 'Amount (INR)']);

                foreach ($allFunds as $index => $row) {
                    fputcsv($file, [
                        $index + 1,
                        $row['formatted_date'],
                        $row['category'],
                        $row['agency_project_no'],
                        $row['project_id'],
                        $row['applicant_name'],
                        $row['agency'],
                        number_format($row['amount'], 2, '.', '')
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Summary calculations
        $totalAmount = $allFunds->sum('amount');
        $orphanAmount = $orphanFunds->sum('amount');
        $daAmount = $daFunds->sum('amount');
        $faAmount = $faFunds->sum('amount');

        $totalCount = $allFunds->count();
        $orphanCount = $orphanFunds->count();
        $daCount = $daFunds->count();
        $faCount = $faFunds->count();

        $agencies = $allFunds->pluck('agency')->unique()->filter()->sort()->values();

        $agencySummary = $allFunds->groupBy('agency')->map(function($items, $agencyName) {
            return [
                'agency' => $agencyName ?: 'Unassigned / Direct',
                'count' => $items->count(),
                'total_amount' => $items->sum('amount'),
                'categories' => $items->pluck('category')->unique()->implode(', '),
            ];
        })->sortByDesc('total_amount')->values();

        return view('admin.reports.social_aid_funds', compact(
            'allFunds',
            'totalAmount',
            'orphanAmount',
            'daAmount',
            'faAmount',
            'totalCount',
            'orphanCount',
            'daCount',
            'faCount',
            'agencies',
            'agencySummary'
        ));
    }

    public function globalSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 1) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $results = [];

        // Parse Application ID (e.g., APLRCFI26EC00008, APLRCFI26OC00007, APL8, 8)
        $numericId = null;
        $matchedCode = null;
        if (preg_match('/APL(?:RCFI)?(?:\d{2})?([A-Za-z]{1,3})?(\d+)/i', $q, $m)) {
            if (!empty($m[2])) {
                $numericId = (int)$m[2];
            }
            if (!empty($m[1])) {
                $matchedCode = strtoupper($m[1]);
            }
        } elseif (is_numeric($q)) {
            $numericId = (int)$q;
        }

        $applicationCategories = [
            ['model' => \App\Models\OrphanCareApplication::class, 'name' => 'Orphan Care', 'slug' => 'orphan-care', 'code' => 'OC'],
            ['model' => \App\Models\DifferentlyAbledApplication::class, 'name' => 'Differently Abled', 'slug' => 'differently-abled', 'code' => 'DA'],
            ['model' => \App\Models\FamilyAidApplication::class, 'name' => 'Family Aid', 'slug' => 'family-aid', 'code' => 'FA'],
            ['model' => \App\Models\EducationCenterApplication::class, 'name' => 'Education Center', 'slug' => 'education-center', 'code' => 'EC'],
            ['model' => \App\Models\CulturalCenterApplication::class, 'name' => 'Cultural Center', 'slug' => 'cultural-center', 'code' => 'CC'],
            ['model' => \App\Models\HospitalClinicApplication::class, 'name' => 'Hospital / Clinic', 'slug' => 'hospital-or-clinics', 'code' => 'HC'],
            ['model' => \App\Models\ShopOtherApplication::class, 'name' => 'Shops & Others', 'slug' => 'shops-and-others', 'code' => 'SO'],
            ['model' => \App\Models\HouseApplication::class, 'name' => 'House', 'slug' => 'house', 'code' => 'H'],
            ['model' => \App\Models\DrinkingWaterIndividualApplication::class, 'name' => 'Drinking Water (Ind)', 'slug' => 'drinking-water-individual', 'code' => 'DWI'],
            ['model' => \App\Models\DrinkingWaterGroupApplication::class, 'name' => 'Drinking Water (Group)', 'slug' => 'drinking-water-group', 'code' => 'DWG'],
            ['model' => \App\Models\GeneralApplication::class, 'name' => 'General', 'slug' => 'general', 'code' => 'GN'],
        ];

        foreach ($applicationCategories as $cat) {
            $modelClass = $cat['model'];
            if (!class_exists($modelClass)) continue;

            $code = $cat['code'];
            $categoryName = $cat['name'];
            $categorySlug = $cat['slug'];

            // Skip if user searched a specific category code (like EC) and this category isn't it
            if ($matchedCode && $matchedCode !== $code && !in_array($matchedCode, ['APL', 'RCFI'])) {
                continue;
            }

            $modelInstance = new $modelClass();
            $table = $modelInstance->getTable();

            $query = $modelClass::query();
            if (method_exists($modelInstance, 'address')) {
                $query->with('address');
            }

            $items = $query->where(function($subQuery) use ($q, $numericId, $table, $modelInstance) {
                if ($numericId) {
                    $subQuery->orWhere('id', $numericId);
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'applicant_name')) {
                    $subQuery->orWhere('applicant_name', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'committee_name')) {
                    $subQuery->orWhere('committee_name', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'agency_number')) {
                    $subQuery->orWhere('agency_number', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'contact_email')) {
                    $subQuery->orWhere('contact_email', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'reg_number')) {
                    $subQuery->orWhere('reg_number', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'mahallu_name')) {
                    $subQuery->orWhere('mahallu_name', 'LIKE', "%{$q}%");
                }

                foreach (['place', 'village', 'panchayat', 'district', 'state', 'post_office', 'house_name', 'locality_location', 'locality_village', 'locality_district', 'locality_state'] as $col) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn($table, $col)) {
                        $subQuery->orWhere($col, 'LIKE', "%{$q}%");
                    }
                }

                if (method_exists($modelInstance, 'address')) {
                    $subQuery->orWhereHas('address', function($addrQuery) use ($q) {
                        $addrQuery->where('place', 'LIKE', "%{$q}%")
                                  ->orWhere('village', 'LIKE', "%{$q}%")
                                  ->orWhere('panchayat', 'LIKE', "%{$q}%")
                                  ->orWhere('district', 'LIKE', "%{$q}%")
                                  ->orWhere('state', 'LIKE', "%{$q}%")
                                  ->orWhere('post_office', 'LIKE', "%{$q}%")
                                  ->orWhere('house_name', 'LIKE', "%{$q}%")
                                  ->orWhere('contact_number_1', 'LIKE', "%{$q}%")
                                  ->orWhere('contact_number_2', 'LIKE', "%{$q}%");
                    });
                }
            })->take(10)->get();

            foreach ($items as $app) {
                $yearStr = !empty($app->created_at) ? date('y', strtotime($app->created_at)) : '26';
                $formattedAppId = 'APLRCFI' . $yearStr . $code . str_pad($app->id, 5, '0', STR_PAD_LEFT);
                $url = route('applications.show', $app->id) . '?category=' . urlencode($categorySlug);
                $name = $app->applicant_name ?? $app->committee_name ?? ('Application #' . $app->id);

                $addr = method_exists($app, 'address') ? $app->address : null;
                $phone = $addr->contact_number_1 ?? $addr->contact_number_2 ?? $app->contact_number_1 ?? 'N/A';
                $locParts = array_filter([
                    $addr->place ?? $app->place ?? $app->locality_location ?? null,
                    $addr->village ?? $app->village ?? $app->locality_village ?? null,
                    $addr->district ?? $app->district ?? $app->locality_district ?? null,
                    $addr->state ?? $app->state ?? $app->locality_state ?? null
                ]);
                $location = implode(', ', $locParts);

                $results[] = [
                    'type' => 'application',
                    'id' => $app->id,
                    'app_id' => $formattedAppId,
                    'name' => $name,
                    'category' => $categoryName,
                    'category_slug' => $categorySlug,
                    'phone' => $phone,
                    'location' => $location ?: 'N/A',
                    'photo' => !empty($app->student_photo) ? asset($app->student_photo) : null,
                    'url' => $url
                ];
            }
        }

        // Search Projects
        $projectCategories = [
            ['model' => \App\Models\OrphanCareProject::class, 'name' => 'Orphan Care', 'slug' => 'orphan-care'],
            ['model' => \App\Models\DifferentlyAbledProject::class, 'name' => 'Differently Abled', 'slug' => 'differently-abled'],
            ['model' => \App\Models\FamilyAidProject::class, 'name' => 'Family Aid', 'slug' => 'family-aid'],
            ['model' => \App\Models\EducationCenterProject::class, 'name' => 'Education Center', 'slug' => 'education-center'],
            ['model' => \App\Models\CulturalCenterProject::class, 'name' => 'Cultural Center', 'slug' => 'cultural-center'],
            ['model' => \App\Models\HospitalClinicProject::class, 'name' => 'Hospital / Clinic', 'slug' => 'hospital-or-clinics'],
            ['model' => \App\Models\ShopOtherProject::class, 'name' => 'Shops & Others', 'slug' => 'shops-and-others'],
            ['model' => \App\Models\HouseProject::class, 'name' => 'House', 'slug' => 'house'],
            ['model' => \App\Models\DrinkingWaterIndividualProject::class, 'name' => 'Drinking Water (Ind)', 'slug' => 'drinking-water-individual'],
            ['model' => \App\Models\DrinkingWaterGroupProject::class, 'name' => 'Drinking Water (Group)', 'slug' => 'drinking-water-group'],
            ['model' => \App\Models\GeneralProject::class, 'name' => 'General', 'slug' => 'general'],
        ];

        foreach ($projectCategories as $pcat) {
            $pModelClass = $pcat['model'];
            if (!class_exists($pModelClass)) continue;

            $pTable = (new $pModelClass())->getTable();
            $query = $pModelClass::query();

            $pItems = $query->where(function($subQuery) use ($q, $numericId, $pTable) {
                if ($numericId) {
                    $subQuery->orWhere('id', $numericId);
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn($pTable, 'project_id')) {
                    $subQuery->orWhere('project_id', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($pTable, 'agency_project_no')) {
                    $subQuery->orWhere('agency_project_no', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($pTable, 'project_name')) {
                    $subQuery->orWhere('project_name', 'LIKE', "%{$q}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($pTable, 'name')) {
                    $subQuery->orWhere('name', 'LIKE', "%{$q}%");
                }
            })->take(5)->get();

            foreach ($pItems as $proj) {
                $url = route('projects.show', $proj->id) . '?type=' . urlencode($pcat['name']);
                $projName = $proj->project_name ?? $proj->name ?? ('Project #' . $proj->id);
                $projIdStr = $proj->project_id ?? $proj->agency_project_no ?? ('PROJ' . $proj->id);

                $results[] = [
                    'type' => 'project',
                    'id' => $proj->id,
                    'app_id' => $projIdStr,
                    'agency_no' => $proj->agency_project_no ?? 'N/A',
                    'name' => $projName,
                    'category' => $pcat['name'] . ' Project',
                    'category_slug' => $pcat['slug'],
                    'phone' => 'N/A',
                    'location' => 'N/A',
                    'photo' => null,
                    'url' => $url
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => array_slice($results, 0, 15)
        ]);
    }

    public function projectReport(Request $request)
    {
        $projectCategories = [
            'orphan-care' => ['model' => \App\Models\OrphanCareProject::class, 'name' => 'Orphan Care', 'slug' => 'orphan-care'],
            'differently-abled' => ['model' => \App\Models\DifferentlyAbledProject::class, 'name' => 'Differently Abled', 'slug' => 'differently-abled'],
            'family-aid' => ['model' => \App\Models\FamilyAidProject::class, 'name' => 'Family Aid', 'slug' => 'family-aid'],
            'education-center' => ['model' => \App\Models\EducationCenterProject::class, 'name' => 'Education Center', 'slug' => 'education-center'],
            'cultural-center' => ['model' => \App\Models\CulturalCenterProject::class, 'name' => 'Cultural Center', 'slug' => 'cultural-center'],
            'hospital-or-clinics' => ['model' => \App\Models\HospitalClinicProject::class, 'name' => 'Hospital or Clinics', 'slug' => 'hospital-or-clinics'],
            'shops-and-others' => ['model' => \App\Models\ShopOtherProject::class, 'name' => 'Shops and Others', 'slug' => 'shops-and-others'],
            'house' => ['model' => \App\Models\HouseProject::class, 'name' => 'House', 'slug' => 'house'],
            'drinking-water-individual' => ['model' => \App\Models\DrinkingWaterIndividualProject::class, 'name' => 'Drinking Water Individual', 'slug' => 'drinking-water-individual'],
            'drinking-water-group' => ['model' => \App\Models\DrinkingWaterGroupProject::class, 'name' => 'Drinking Water Group', 'slug' => 'drinking-water-group'],
            'general' => ['model' => \App\Models\GeneralProject::class, 'name' => 'General', 'slug' => 'general'],
        ];

        $allProjects = collect();

        foreach ($projectCategories as $catSlug => $cat) {
            $modelClass = $cat['model'];
            if (!class_exists($modelClass)) continue;

            $modelInstance = new $modelClass();
            $query = $modelClass::query();
            if (method_exists($modelInstance, 'funds')) {
                $query->with(['funds.donorModel']);
            }

            $items = $query->get();

            foreach ($items as $proj) {
                $projIdStr = $proj->project_id ?? $proj->agency_project_no ?? ('PROJ' . str_pad($proj->id, 5, '0', STR_PAD_LEFT));
                $projName = $proj->project_name ?? $proj->name ?? ('Project #' . $proj->id);

                // Extract actual donor names from funds or fallback
                $donorNames = collect();
                if ($proj->relationLoaded('funds') && $proj->funds->count() > 0) {
                    foreach ($proj->funds as $f) {
                        if ($f->donorModel) {
                            $dName = $f->donorModel->name;
                            if (!empty($f->donorModel->short_name)) {
                                $dName .= ' (' . $f->donorModel->short_name . ')';
                            }
                            $donorNames->push($dName);
                        } elseif (!empty($f->donor)) {
                            $donorNames->push($f->donor);
                        }
                    }
                }

                $sponsor = $donorNames->unique()->filter()->implode(', ');
                if (empty($sponsor)) {
                    $rawSponsor = $proj->sponsor ?? $proj->donor ?? 'N/A';
                    $sponsor = ($rawSponsor === 'Sponsored') ? 'Sponsored (Direct)' : $rawSponsor;
                }

                $stage = $proj->stage ?? $proj->current_phase ?? 1;
                $status = $proj->status ?? (!empty($proj->suspend_status) && $proj->suspend_status === 'Suspended' ? 'Suspended' : 'Active');

                $amount = 0;
                if ($proj->relationLoaded('funds') && $proj->funds->count() > 0) {
                    $amount = (float)$proj->funds->sum('amount');
                } elseif (isset($proj->budget)) {
                    $amount = (float)$proj->budget;
                }

                $createdAt = $proj->created_at ? $proj->created_at->format('Y-m-d') : null;
                $formattedCreatedAt = $proj->created_at ? $proj->created_at->format('d M, Y') : 'N/A';

                $url = route('projects.show', $proj->id) . '?type=' . urlencode($cat['name']);

                $allProjects->push([
                    'id' => $proj->id,
                    'project_id_str' => $projIdStr,
                    'name' => $projName,
                    'category' => $cat['name'],
                    'category_slug' => $catSlug,
                    'agency_project_no' => $proj->agency_project_no ?? 'N/A',
                    'sponsor' => $sponsor,
                    'stage' => $stage,
                    'status' => $status,
                    'amount' => $amount,
                    'created_at' => $createdAt,
                    'formatted_created_date' => $formattedCreatedAt,
                    'url' => $url
                ]);
            }
        }

        // Search Filter
        $search = trim($request->input('search', ''));
        if ($search !== '') {
            $allProjects = $allProjects->filter(function($item) use ($search) {
                return str_contains(strtolower($item['project_id_str']), strtolower($search))
                    || str_contains(strtolower($item['name']), strtolower($search))
                    || str_contains(strtolower($item['agency_project_no']), strtolower($search))
                    || str_contains(strtolower($item['sponsor']), strtolower($search))
                    || str_contains(strtolower($item['category']), strtolower($search));
            });
        }

        // Category Filter
        $categoryFilter = $request->input('category', '');
        if ($categoryFilter !== '') {
            $allProjects = $allProjects->where('category_slug', $categoryFilter);
        }

        // Status Filter
        $statusFilter = $request->input('status', '');
        if ($statusFilter !== '') {
            $allProjects = $allProjects->where('status', $statusFilter);
        }

        // Date Filter
        $datePreset = $request->input('date_preset', '');
        $fromDate = $request->input('from_date', '');
        $toDate = $request->input('to_date', '');

        if ($datePreset !== '' || ($fromDate && $toDate)) {
            $today = \Carbon\Carbon::today();

            if ($datePreset === 'today') {
                $fromDate = $today->format('Y-m-d');
                $toDate = $today->format('Y-m-d');
            } elseif ($datePreset === 'this_week') {
                $fromDate = $today->copy()->startOfWeek()->format('Y-m-d');
                $toDate = $today->copy()->endOfWeek()->format('Y-m-d');
            } elseif ($datePreset === 'this_month') {
                $fromDate = $today->copy()->startOfMonth()->format('Y-m-d');
                $toDate = $today->copy()->endOfMonth()->format('Y-m-d');
            } elseif ($datePreset === 'this_year') {
                $fromDate = $today->copy()->startOfYear()->format('Y-m-d');
                $toDate = $today->copy()->endOfYear()->format('Y-m-d');
            }

            if ($fromDate && $toDate) {
                $allProjects = $allProjects->filter(function($item) use ($fromDate, $toDate) {
                    if (!$item['created_at']) return false;
                    return $item['created_at'] >= $fromDate && $item['created_at'] <= $toDate;
                });
            }
        }

        // Export CSV
        if ($request->input('export') === 'csv') {
            $fileName = 'All_Projects_Report_' . date('Y-m-d_H-i') . '.csv';
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $callback = function() use ($allProjects) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['SL', 'Project ID', 'Project Name', 'Category', 'Agency Project No', 'Sponsor / Donor', 'Stage', 'Status', 'Total Amount (INR)', 'Created Date']);

                $sl = 1;
                foreach ($allProjects as $row) {
                    fputcsv($file, [
                        $sl++,
                        $row['project_id_str'],
                        $row['name'],
                        $row['category'],
                        $row['agency_project_no'],
                        $row['sponsor'],
                        'Stage ' . $row['stage'],
                        $row['status'],
                        number_format($row['amount'], 2, '.', ''),
                        $row['formatted_created_date']
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $totalProjects = $allProjects->count();
        $activeProjects = $allProjects->where('status', 'Active')->count();
        $completedProjects = $allProjects->where('status', 'Completed')->count();
        $suspendedProjects = $allProjects->where('status', 'Suspended')->count();
        $totalAmount = $allProjects->sum('amount');

        $categoriesList = collect($projectCategories)->pluck('name', 'slug');

        return view('admin.reports.projects', compact(
            'allProjects',
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'suspendedProjects',
            'totalAmount',
            'categoriesList'
        ));
    }

    public function singleProjectReport(Request $request, $id = null)
    {
        $projectCategories = [
            'orphan-care' => ['model' => \App\Models\OrphanCareProject::class, 'name' => 'Orphan Care'],
            'differently-abled' => ['model' => \App\Models\DifferentlyAbledProject::class, 'name' => 'Differently Abled'],
            'family-aid' => ['model' => \App\Models\FamilyAidProject::class, 'name' => 'Family Aid'],
            'education-center' => ['model' => \App\Models\EducationCenterProject::class, 'name' => 'Education Center'],
            'cultural-center' => ['model' => \App\Models\CulturalCenterProject::class, 'name' => 'Cultural Center'],
            'hospital-or-clinics' => ['model' => \App\Models\HospitalClinicProject::class, 'name' => 'Hospital or Clinics'],
            'shops-and-others' => ['model' => \App\Models\ShopOtherProject::class, 'name' => 'Shops and Others'],
            'house' => ['model' => \App\Models\HouseProject::class, 'name' => 'House'],
            'drinking-water-individual' => ['model' => \App\Models\DrinkingWaterIndividualProject::class, 'name' => 'Drinking Water Individual'],
            'drinking-water-group' => ['model' => \App\Models\DrinkingWaterGroupProject::class, 'name' => 'Drinking Water Group'],
            'general' => ['model' => \App\Models\GeneralProject::class, 'name' => 'General'],
        ];

        $allProjectsList = collect();

        foreach ($projectCategories as $catSlug => $cat) {
            $modelClass = $cat['model'];
            if (!class_exists($modelClass)) continue;

            $items = $modelClass::all();
            foreach ($items as $p) {
                $pIdStr = $p->project_id ?? $p->agency_project_no ?? ('PROJ' . str_pad($p->id, 5, '0', STR_PAD_LEFT));
                $pName = $p->project_name ?? $p->name ?? ('Project #' . $p->id);

                $allProjectsList->push([
                    'id' => $p->id,
                    'key' => $catSlug . '_' . $p->id,
                    'category_slug' => $catSlug,
                    'category' => $cat['name'],
                    'project_id_str' => $pIdStr,
                    'name' => $pName,
                    'model' => $modelClass,
                    'instance' => $p
                ]);
            }
        }

        if ($allProjectsList->isEmpty()) {
            return view('admin.reports.single_project', [
                'selectedProject' => null,
                'allProjectsList' => collect()
            ]);
        }

        $targetProjectData = null;
        $categoryParam = $request->input('category');
        $keyParam = $request->input('key');

        if ($id) {
            if ($categoryParam) {
                $targetProjectData = $allProjectsList->firstWhere(function($item) use ($id, $categoryParam) {
                    return (string)$item['id'] === (string)$id && (
                        $item['category_slug'] === $categoryParam ||
                        str_starts_with($item['category_slug'], $categoryParam) ||
                        str_starts_with($categoryParam, $item['category_slug'])
                    );
                });
            }
            if (!$targetProjectData) {
                $targetProjectData = $allProjectsList->firstWhere('id', (int)$id);
            }
        } elseif ($keyParam) {
            $targetProjectData = $allProjectsList->firstWhere('key', $keyParam);
        }

        if (!$targetProjectData) {
            $targetProjectData = $allProjectsList->first();
        }

        $projectObj = $targetProjectData['instance'];
        $modelClass = $targetProjectData['model'];
        $categorySlug = $targetProjectData['category_slug'];

        $projectManager = null;
        $engineer = null;
        $contractor = null;
        $application = null;

        if (isset($projectObj->manager_id)) {
            $projectManager = \App\Models\User::find($projectObj->manager_id);
        } elseif (isset($projectObj->project_manager_id)) {
            $projectManager = \App\Models\User::find($projectObj->project_manager_id);
        }

        if (isset($projectObj->engineer_id)) {
            $engineer = \App\Models\User::find($projectObj->engineer_id);
        }

        if (isset($projectObj->contractor_id)) {
            $contractor = \App\Models\Contractor::find($projectObj->contractor_id);
        }

        if (isset($projectObj->application_id)) {
            $appId = $projectObj->application_id;
            $appModels = [
                \App\Models\OrphanCareApplication::class,
                \App\Models\DifferentlyAbledApplication::class,
                \App\Models\FamilyAidApplication::class,
                \App\Models\EducationCenterApplication::class,
                \App\Models\CulturalCenterApplication::class,
                \App\Models\HospitalClinicApplication::class,
                \App\Models\ShopOtherApplication::class,
                \App\Models\HouseApplication::class,
                \App\Models\DrinkingWaterIndividualApplication::class,
                \App\Models\DrinkingWaterGroupApplication::class,
                \App\Models\GeneralApplication::class,
            ];
            foreach ($appModels as $aClass) {
                if (class_exists($aClass)) {
                    $foundApp = $aClass::find($appId);
                    if ($foundApp) {
                        $application = $foundApp;
                        break;
                    }
                }
            }
        }

        $projectTypeMorph = $modelClass;

        $projectDocument = \App\Models\ProjectDocument::where('project_id', $projectObj->id)
            ->where('project_type', $projectTypeMorph)
            ->first();
        if (!$projectDocument) {
            $projectDocument = \App\Models\ProjectDocument::where('project_id', $projectObj->id)->first();
        }

        $completionDetail = \App\Models\ProjectCompletionDetail::where('project_id', $projectObj->id)
            ->where('project_type', $projectTypeMorph)
            ->first();
        if (!$completionDetail) {
            $completionDetail = \App\Models\ProjectCompletionDetail::where('project_id', $projectObj->id)->first();
        }

        $inspections = \App\Models\ProjectInspection::where('project_id', $projectObj->id)
            ->where('project_type', $projectTypeMorph)
            ->get();
        if ($inspections->isEmpty()) {
            $inspections = \App\Models\ProjectInspection::where('project_id', $projectObj->id)->get();
        }

        $communityContributions = \App\Models\ProjectCommunityContribution::where('project_id', $projectObj->id)
            ->where('project_type', $projectTypeMorph)
            ->get();

        $totalAllocated = 0;
        if (method_exists($projectObj, 'funds')) {
            $totalAllocated = (float)$projectObj->funds()->sum('amount');
        } elseif (isset($projectObj->budget)) {
            $totalAllocated = (float)$projectObj->budget;
        }

        $totalCommunityContrib = $communityContributions->sum('amount');
        if ($completionDetail && $completionDetail->community_contribution) {
            $totalCommunityContrib = (float)$completionDetail->community_contribution;
        }

        $totalGrants = $totalAllocated;
        $leverage = $completionDetail->amount_paid_by_donor ?? 0;
        $anyOther = $completionDetail->any_other ?? 0;
        $deductions = $completionDetail->deductions ?? 0;

        $totalProjectCost = $completionDetail->total_project_cost ?? ($totalAllocated + $totalCommunityContrib + $anyOther - $deductions);

        $docFields = [
            'land_document' => 'Land document',
            'possession_certificate' => 'Possession certificate',
            'recommendation_letter' => 'Recommendation letter',
            'committee_minutes' => 'Committee minutes',
            'permit_copy' => 'Permit copy',
            'plan' => 'Plan',
            'tender_schedule_sheet' => 'Tender schedule sheet',
            'site_study' => 'Site study',
            'quotations' => 'Quotations',
            'quotations_approval_form' => 'Quotations approval form',
            'work_order_letter' => 'Work order letter',
            'meeting_minutes_copy' => 'Meeting minutes copy',
            'agreement_with_contractor' => 'Agreement with contractor',
            'agreement_with_committee' => 'Agreement with committee',
            'project_summary_form' => 'Project summary form',
        ];

        return view('admin.reports.single_project', compact(
            'targetProjectData',
            'projectObj',
            'allProjectsList',
            'projectManager',
            'engineer',
            'contractor',
            'application',
            'projectDocument',
            'completionDetail',
            'inspections',
            'communityContributions',
            'totalAllocated',
            'totalCommunityContrib',
            'totalGrants',
            'leverage',
            'anyOther',
            'deductions',
            'totalProjectCost',
            'docFields'
        ));
    }
}
