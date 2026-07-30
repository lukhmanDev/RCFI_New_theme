@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

    <!-- Welcoming Header -->
    <div class="welcome-banner" style="display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 1px solid #a7f3d0; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.04);">
        <div style="flex: 1; z-index: 2;">
            <h1 style="color: #065f46; font-size: 1.75rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">Welcome back, {{ Auth::user()->name }}! <span style="animation: wave 2s infinite; transform-origin: 70% 70%; display: inline-block;">👋</span></h1>
            <p style="color: #047857; font-size: 0.95rem; margin-top: 0.4rem; margin-bottom: 0;">Here's what's happening with RCFI today.</p>
        </div>
        <div class="banner-illustration" style="flex-shrink: 0; width: 140px; display: flex; align-items: center; justify-content: center; z-index: 2;">
            <svg width="110" height="90" viewBox="0 0 110 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Doughnut Chart Background -->
                <circle cx="75" cy="40" r="28" fill="#a7f3d0" fill-opacity="0.5"/>
                <circle cx="75" cy="40" r="20" stroke="#10b981" stroke-width="5" stroke-dasharray="80 50"/>
                <circle cx="75" cy="40" r="20" stroke="#059669" stroke-width="5" stroke-dasharray="30 100" stroke-dashoffset="-80"/>
                
                <!-- Floating File/Card -->
                <rect x="15" y="25" width="45" height="55" rx="6" fill="#ffffff" stroke="#e2e8f0" stroke-width="2"/>
                <rect x="23" y="38" width="28" height="4" rx="2" fill="#34d399"/>
                <rect x="23" y="48" width="20" height="4" rx="2" fill="#a7f3d0"/>
                <rect x="23" y="58" width="24" height="4" rx="2" fill="#e2e8f0"/>
                
                <!-- Little Success Badge on Card -->
                <circle cx="48" cy="68" r="7" fill="#10b981"/>
                <path d="M45.5 68L47 69.5L50.5 66.5" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>

                <!-- Potted Plant Leaf illustration -->
                <path d="M90 65C90 55 98 52 98 52C98 52 95 62 90 65Z" fill="#a7f3d0"/>
                <path d="M98 68C98 58 105 55 105 55C105 55 102 65 98 68Z" fill="#34d399"/>
                <rect x="91" y="68" width="12" height="12" rx="2" fill="#f59e0b"/>
            </svg>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        <!-- Card 1: Registered Users -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); transition: transform 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Registered Users</span>
                    <h2 style="color: #1e293b; font-size: 1.75rem; font-weight: 700; margin-top: 0.3rem; margin-bottom: 0;">{{ $userCount }}</h2>
                </div>
                <div style="background: #eff6ff; color: #3b82f6; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="bx bx-group"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.85rem; border-top: 1px dashed #f1f5f9; padding-top: 0.65rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.8rem; font-weight: 600; color: #10b981;">
                    <span>↑ 100%</span>
                </div>
                <div style="width: 60px; height: 20px;">
                    <svg width="60" height="20" viewBox="0 0 60 20">
                        <path d="M0,18 Q10,16 20,8 T40,12 T60,2" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Total Applications -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); transition: transform 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Applications</span>
                    <h2 style="color: #1e293b; font-size: 1.75rem; font-weight: 700; margin-top: 0.3rem; margin-bottom: 0;">{{ $applicationsCount }}</h2>
                </div>
                <div style="background: #ecfdf5; color: #10b981; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="bx bx-file"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.85rem; border-top: 1px dashed #f1f5f9; padding-top: 0.65rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.8rem; font-weight: 600; color: #10b981;">
                    <span>All Time</span>
                </div>
                <div style="width: 60px; height: 20px;">
                    <svg width="60" height="20" viewBox="0 0 60 20">
                        <path d="M0,18 Q10,14 20,10 T40,12 T60,2" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Today Applications -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); transition: transform 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Today Applications</span>
                    <h2 style="color: #0ea5e9; font-size: 1.75rem; font-weight: 700; margin-top: 0.3rem; margin-bottom: 0;">{{ $todayCount }}</h2>
                </div>
                <div style="background: #e0f2fe; color: #0ea5e9; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="bx bx-calendar-event"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.85rem; border-top: 1px dashed #f1f5f9; padding-top: 0.65rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.8rem; font-weight: 600; color: #0ea5e9;">
                    <span>Today</span>
                </div>
                <div style="width: 60px; height: 20px;">
                    <svg width="60" height="20" viewBox="0 0 60 20">
                        <path d="M0,15 Q15,8 30,14 T60,4" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Approved Applications -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); transition: transform 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Approved</span>
                    <h2 style="color: #10b981; font-size: 1.75rem; font-weight: 700; margin-top: 0.3rem; margin-bottom: 0;">{{ $approvedCount }}</h2>
                </div>
                <div style="background: #ecfdf5; color: #10b981; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="bx bx-check-circle"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.85rem; border-top: 1px dashed #f1f5f9; padding-top: 0.65rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.8rem; font-weight: 600; color: #10b981;">
                    <span>Verified</span>
                </div>
                <div style="width: 60px; height: 20px;">
                    <svg width="60" height="20" viewBox="0 0 60 20">
                        <path d="M0,18 Q15,10 30,12 T60,2" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 5: Rejected Applications -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 140px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); transition: transform 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Rejected</span>
                    <h2 style="color: #ef4444; font-size: 1.75rem; font-weight: 700; margin-top: 0.3rem; margin-bottom: 0;">{{ $rejectedCount }}</h2>
                </div>
                <div style="background: #fef2f2; color: #ef4444; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="bx bx-x-circle"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.85rem; border-top: 1px dashed #f1f5f9; padding-top: 0.65rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.8rem; font-weight: 600; color: #ef4444;">
                    <span>Closed</span>
                </div>
                <div style="width: 60px; height: 20px;">
                    <svg width="60" height="20" viewBox="0 0 60 20">
                        <path d="M0,10 L20,12 L40,15 L60,18" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="dashboard-grid two-cols" style="display: grid; grid-template-columns: 1.7fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Left Panel: Applications Overview -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #eaecf0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="color: #101828; font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: -0.01em;">Applications Overview</h3>
                <div style="position: relative;">
                    <select id="overviewFilterSelect" onchange="updateOverviewChartPeriod(this.value)" style="appearance: none; background: #ffffff; border: 1px solid #d0d5dd; border-radius: 8px; padding: 0.45rem 2.25rem 0.45rem 0.85rem; font-size: 0.85rem; font-weight: 600; color: #344054; cursor: pointer; outline: none; box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);">
                        <option value="this_month" selected>This Month</option>
                        <option value="last_7_days">Last 7 Days</option>
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="this_year">This Year</option>
                    </select>
                    <i class="bx bx-chevron-down" style="position: absolute; right: 0.65rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #667085; font-size: 1.1rem;"></i>
                </div>
            </div>
            <div style="height: 280px; position: relative; width: 100%;">
                <canvas id="applicationsOverviewChart"></canvas>
            </div>
        </div>

        <!-- Right Panel: Applications by Status -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #eaecf0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); display: flex; flex-direction: column;">
            <h3 style="color: #101828; font-size: 1.1rem; font-weight: 700; margin-top: 0; margin-bottom: 1.25rem; letter-spacing: -0.01em;">Applications by Status</h3>
            
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex: 1;">
                <!-- Left: Donut Chart with Center Text -->
                <div style="position: relative; width: 160px; height: 160px; flex-shrink: 0;">
                    <canvas id="applicationsStatusChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <span style="font-size: 1.85rem; font-weight: 800; color: #101828; display: block; line-height: 1;">{{ $applicationsCount }}</span>
                        <span style="font-size: 0.78rem; color: #667085; font-weight: 600; margin-top: 2px; display: block;">Total</span>
                    </div>
                </div>

                <!-- Right: Status Breakdown Legend List -->
                @php
                    $totalApps = max(1, $applicationsCount);
                    $approvedPct = round(($approvedCount / $totalApps) * 100, 1);
                    $pendingPct = round(($pendingCount / $totalApps) * 100, 1);
                    $rejectedPct = round(($rejectedCount / $totalApps) * 100, 1);
                @endphp
                <div style="display: flex; flex-direction: column; gap: 0.85rem; flex-grow: 1; padding-left: 0.5rem;">
                    <!-- Approved -->
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="width: 12px; height: 12px; background: #22c55e; border-radius: 4px; display: inline-block;"></span>
                            <span style="font-size: 0.88rem; font-weight: 600; color: #344054;">Approved</span>
                        </div>
                        <span style="font-size: 0.88rem; font-weight: 700; color: #344054;">
                            {{ $approvedCount }} <span style="font-size: 0.82rem; font-weight: 500; color: #667085;">({{ $approvedPct }}%)</span>
                        </span>
                    </div>
                    <!-- Pending -->
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="width: 12px; height: 12px; background: #3b82f6; border-radius: 4px; display: inline-block;"></span>
                            <span style="font-size: 0.88rem; font-weight: 600; color: #344054;">Pending</span>
                        </div>
                        <span style="font-size: 0.88rem; font-weight: 700; color: #344054;">
                            {{ $pendingCount }} <span style="font-size: 0.82rem; font-weight: 500; color: #667085;">({{ $pendingPct }}%)</span>
                        </span>
                    </div>
                    <!-- Rejected -->
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="width: 12px; height: 12px; background: #94a3b8; border-radius: 4px; display: inline-block;"></span>
                            <span style="font-size: 0.88rem; font-weight: 600; color: #344054;">Rejected</span>
                        </div>
                        <span style="font-size: 0.88rem; font-weight: 700; color: #344054;">
                            {{ $rejectedCount }} <span style="font-size: 0.82rem; font-weight: 500; color: #667085;">({{ $rejectedPct }}%)</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row Section -->
    <div class="dashboard-grid two-cols" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Left Panel: Recent Applications -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #1e293b; font-size: 1rem; font-weight: 700; margin: 0;">Recent Submissions</h3>
                <a href="{{ route('applications.index') }}" style="color: #6366f1; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                    View All <i class="bx bx-right-arrow-alt" style="font-size: 1.1rem;"></i>
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse($recentApplications as $recent)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.9rem 1.1rem; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9; transition: background 0.2s;">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; color: #6366f1; font-size: 1.1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <i class="bx bx-file-blank"></i>
                            </div>
                            <div>
                                <h4 style="color: #1e293b; font-size: 0.9rem; font-weight: 600; margin: 0; line-height: 1.2;">{{ $recent['applicant_name'] }}</h4>
                                <span style="color: #64748b; font-size: 0.78rem; font-weight: 500;">{{ $recent['category_name'] }} • {{ $recent['created_at'] ? $recent['created_at']->diffForHumans() : 'Recently' }}</span>
                            </div>
                        </div>
                        <div>
                            @if($recent['status'] === 'Approved')
                                <span style="background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class="bx bx-check-circle"></i> Approved
                                </span>
                            @elseif($recent['status'] === 'Rejected')
                                <span style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class="bx bx-x-circle"></i> Rejected
                                </span>
                            @else
                                <span style="background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class="bx bx-time-five"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem 1rem; color: #94a3b8; font-size: 0.88rem;">
                        No applications submitted yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Panel: Quick Actions -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="color: #1e293b; font-size: 1rem; font-weight: 700; margin-top: 0; margin-bottom: 1.25rem;">Quick Management</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <a href="{{ route('applications.index') }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#6366f1'; this.style.background='#eef2ff';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                        <div style="width: 42px; height: 42px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="bx bx-list-ul"></i>
                        </div>
                        <span style="color: #1e293b; font-size: 0.85rem; font-weight: 600; text-align: center;">All Applications</span>
                    </a>

                    <a href="{{ route('applications.approved.index') }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#10b981'; this.style.background='#ecfdf5';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                        <div style="width: 42px; height: 42px; border-radius: 10px; background: #d1fae5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="bx bx-check-shield"></i>
                        </div>
                        <span style="color: #1e293b; font-size: 0.85rem; font-weight: 600; text-align: center;">Approved Lists</span>
                    </a>

                    <a href="{{ route('users') }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#3b82f6'; this.style.background='#eff6ff';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                        <div style="width: 42px; height: 42px; border-radius: 10px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="bx bx-user-check"></i>
                        </div>
                        <span style="color: #1e293b; font-size: 0.85rem; font-weight: 600; text-align: center;">User Roles</span>
                    </a>

                    <a href="{{ route('donors.index') }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#8b5cf6'; this.style.background='#f5f3ff';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                        <div style="width: 42px; height: 42px; border-radius: 10px; background: #ede9fe; color: #6d28d9; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="bx bx-building"></i>
                        </div>
                        <span style="color: #1e293b; font-size: 0.85rem; font-weight: 600; text-align: center;">Partners</span>
                    </a>
                </div>
            </div>

            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; display: flex; align-items: center; justify-content: space-between; font-size: 0.82rem; color: #64748b;">
                <span>System Status</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; color: #10b981; font-weight: 600;">
                    <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span> Active &amp; Synced
                </span>
            </div>
        </div>
    </div>

</div>

<script>
    const chartPeriodData = {!! json_encode($chartPeriodData ?? [
        'this_month' => ['labels' => ['May 1', 'May 7', 'May 13', 'May 19', 'May 25', 'May 31'], 'data' => [3, 11, 13, 10, 13, 20]],
        'last_7_days' => ['labels' => ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'], 'data' => [4, 8, 12, 15, 10, 18, 22]],
        'last_30_days' => ['labels' => ['W1', 'W2', 'W3', 'W4', 'W5'], 'data' => [5, 12, 18, 25, 30]],
        'this_year' => ['labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], 'data' => [10, 25, 40, 65, 80, 100]],
    ]) !!};

    let overviewChartInstance = null;
    let statusChartInstance = null;

    function updateOverviewChartPeriod(periodKey) {
        if (!overviewChartInstance || !chartPeriodData[periodKey]) return;
        const period = chartPeriodData[periodKey];
        overviewChartInstance.data.labels = period.labels;
        overviewChartInstance.data.datasets[0].data = period.data;
        overviewChartInstance.update('active');
    }

    let chartRetryCount = 0;
    function renderAdminDashboardCharts() {
        if (typeof Chart === 'undefined') {
            if (chartRetryCount < 40) {
                chartRetryCount++;
                setTimeout(renderAdminDashboardCharts, 50);
            } else {
                console.error('Chart.js failed to load within timeout.');
            }
            return;
        }

        const overviewCanvas = document.getElementById('applicationsOverviewChart');
        const statusCanvas = document.getElementById('applicationsStatusChart');

        if (!overviewCanvas || !statusCanvas) {
            return;
        }

        const fontFamily = getComputedStyle(document.body).fontFamily || 'Inter, sans-serif';
        Chart.defaults.font.family = fontFamily;
        Chart.defaults.color = '#64748b';

        // 1. Smooth Purple Area Line Chart: Applications Overview
        try {
            const existingOverviewChart = Chart.getChart(overviewCanvas);
            if (existingOverviewChart) {
                existingOverviewChart.destroy();
            } else if (overviewChartInstance) {
                try { overviewChartInstance.destroy(); } catch (e) {}
                overviewChartInstance = null;
            }

            const ctx = overviewCanvas.getContext('2d');
            if (ctx) {
                const emeraldGradient = ctx.createLinearGradient(0, 0, 0, 260);
                emeraldGradient.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
                emeraldGradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

                const initialPeriod = chartPeriodData['this_month'] || {
                    labels: {!! json_encode($chartLabels ?? ['May 1', 'May 7', 'May 13', 'May 19', 'May 25', 'May 31']) !!},
                    data: {!! json_encode($chartAllData ?? [3, 11, 13, 10, 13, 20]) !!}
                };

                overviewChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: initialPeriod.labels,
                        datasets: [{
                            label: 'Applications',
                            data: initialPeriod.data,
                            borderColor: '#10b981',
                            backgroundColor: emeraldGradient,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2.5,
                            pointHoverBackgroundColor: '#10b981',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: true,
                                backgroundColor: '#ffffff',
                                titleColor: '#101828',
                                titleFont: { size: 13, weight: '700' },
                                bodyColor: '#475569',
                                bodyFont: { size: 12, weight: '600' },
                                borderColor: '#eaecf0',
                                borderWidth: 1,
                                padding: 12,
                                boxPadding: 6,
                                usePointStyle: true,
                                displayColors: true,
                                boxWidth: 8,
                                boxHeight: 8,
                                borderRadius: 10,
                                shadowColor: 'rgba(16, 24, 40, 0.1)',
                                callbacks: {
                                    title: function(tooltipItems) {
                                        const label = tooltipItems[0].label;
                                        return label.includes('20') ? label : label + ', 2025';
                                    },
                                    label: function(context) {
                                        return 'Applications: ' + context.formattedValue;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 11, weight: '500' }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                    strokeDashArray: [4, 4]
                                },
                                border: { dash: [4, 4] },
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 11, weight: '500' },
                                    stepSize: 5
                                }
                            }
                        }
                    }
                });
            }
        } catch (e) {
            console.error('Failed to render Applications Overview chart:', e);
        }

        // 2. Doughnut Chart: Applications Status Breakdown
        try {
            const existingStatusChart = Chart.getChart(statusCanvas);
            if (existingStatusChart) {
                existingStatusChart.destroy();
            } else if (statusChartInstance) {
                try { statusChartInstance.destroy(); } catch (e) {}
                statusChartInstance = null;
            }

            statusChartInstance = new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [
                            {{ $approvedCount }},
                            {{ $pendingCount }},
                            {{ $rejectedCount }}
                        ],
                        backgroundColor: [
                            '#22c55e', // Green for Approved
                            '#3b82f6', // Blue for Pending
                            '#94a3b8'  // Gray for Rejected
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '74%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            borderRadius: 8
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Failed to render Applications Status chart:', e);
        }
    }

    // Trigger chart initialization for initial page load, PJAX updates, and back/forward cache restore
    const safeRenderCharts = () => requestAnimationFrame(renderAdminDashboardCharts);
    setTimeout(safeRenderCharts, 50);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', safeRenderCharts);
    }
    window.addEventListener('pageshow', safeRenderCharts);
    document.addEventListener('pjax:complete', safeRenderCharts);
</script>

@endsection

<style>
    @keyframes wave {
        0% { transform: rotate( 0.0deg) }
        10% { transform: rotate(14.0deg) }
        20% { transform: rotate(-8.0deg) }
        30% { transform: rotate(14.0deg) }
        40% { transform: rotate(-4.0deg) }
        50% { transform: rotate(10.0deg) }
        60% { transform: rotate( 0.0deg) }
        100% { transform: rotate( 0.0deg) }
    }

    .stat-card-premium:hover,
    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.04) !important;
        border-color: #cbd5e1 !important;
    }
</style>
