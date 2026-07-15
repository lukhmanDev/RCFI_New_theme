@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

    <!-- Welcoming Header -->
    <div class="welcome-banner" style="display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid #e2e8f0; border-radius: 16px; padding: 2rem; margin-bottom: 2rem; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.04);">
        <div style="flex: 1; z-index: 2;">
            <h1 style="color: #1e293b; font-size: 1.75rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">Welcome back, {{ Auth::user()->name }}! <span style="animation: wave 2s infinite; transform-origin: 70% 70%; display: inline-block;">👋</span></h1>
            <p style="color: #64748b; font-size: 0.95rem; margin-top: 0.4rem; margin-bottom: 0;">Here's what's happening with RCFI today.</p>
        </div>
        <div class="banner-illustration" style="flex-shrink: 0; width: 140px; display: flex; align-items: center; justify-content: center; z-index: 2;">
            <svg width="110" height="90" viewBox="0 0 110 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Doughnut Chart Background -->
                <circle cx="75" cy="40" r="28" fill="#c7d2fe" fill-opacity="0.5"/>
                <circle cx="75" cy="40" r="20" stroke="#6366f1" stroke-width="5" stroke-dasharray="80 50"/>
                <circle cx="75" cy="40" r="20" stroke="#10b981" stroke-width="5" stroke-dasharray="30 100" stroke-dashoffset="-80"/>
                
                <!-- Floating File/Card -->
                <rect x="15" y="25" width="45" height="55" rx="6" fill="#ffffff" stroke="#e2e8f0" stroke-width="2"/>
                <rect x="23" y="38" width="28" height="4" rx="2" fill="#818cf8"/>
                <rect x="23" y="48" width="20" height="4" rx="2" fill="#a5b4fc"/>
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
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <!-- Card 1: Registered Users -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 145px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02), 0 2px 4px -1px rgba(15, 23, 42, 0.01); transition: transform 0.2s, box-shadow 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Registered Users</span>
                    <h2 style="color: #1e293b; font-size: 1.85rem; font-weight: 700; margin-top: 0.4rem; margin-bottom: 0;">{{ $userCount }}</h2>
                </div>
                <div style="background: #eff6ff; color: #3b82f6; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class="bx bx-group"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; border-top: 1px dashed #f1f5f9; padding-top: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.82rem; font-weight: 600; color: #10b981;">
                    <span>↑ 100%</span>
                    <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </div>
                <div style="width: 70px; height: 25px;">
                    <svg width="70" height="25" viewBox="0 0 70 25">
                        <path d="M0,20 Q10,18 20,10 T40,15 T60,5 T70,2" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Registered Partners -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 145px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02), 0 2px 4px -1px rgba(15, 23, 42, 0.01); transition: transform 0.2s, box-shadow 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Registered Partners</span>
                    <h2 style="color: #1e293b; font-size: 1.85rem; font-weight: 700; margin-top: 0.4rem; margin-bottom: 0;">{{ $donorsCount }}</h2>
                </div>
                <div style="background: #f5f3ff; color: #8b5cf6; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class="bx bx-buildings"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; border-top: 1px dashed #f1f5f9; padding-top: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.82rem; font-weight: 600; color: #64748b;">
                    <span>— 0%</span>
                    <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </div>
                <div style="width: 70px; height: 25px;">
                    <svg width="70" height="25" viewBox="0 0 70 25">
                        <path d="M0,15 L10,15 L20,15 L30,15 L40,15 L50,15 L60,15 L70,15" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Applications -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 145px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02), 0 2px 4px -1px rgba(15, 23, 42, 0.01); transition: transform 0.2s, box-shadow 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Applications</span>
                    <h2 style="color: #1e293b; font-size: 1.85rem; font-weight: 700; margin-top: 0.4rem; margin-bottom: 0;">{{ $applicationsCount }}</h2>
                </div>
                <div style="background: #ecfdf5; color: #10b981; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class="bx bx-file"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; border-top: 1px dashed #f1f5f9; padding-top: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.82rem; font-weight: 600; color: #10b981;">
                    <span>↑ 28.6%</span>
                    <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </div>
                <div style="width: 70px; height: 25px;">
                    <svg width="70" height="25" viewBox="0 0 70 25">
                        <path d="M0,22 Q10,20 20,15 T40,18 T60,8 T70,5" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Approved Applications -->
        <div class="stat-card-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 145px; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02), 0 2px 4px -1px rgba(15, 23, 42, 0.01); transition: transform 0.2s, box-shadow 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Approved Applications</span>
                    <h2 style="color: #1e293b; font-size: 1.85rem; font-weight: 700; margin-top: 0.4rem; margin-bottom: 0;">{{ $approvedCount }}</h2>
                </div>
                <div style="background: #fff7ed; color: #f97316; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class="bx bx-check-circle"></i>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1rem; border-top: 1px dashed #f1f5f9; padding-top: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.82rem; font-weight: 600; color: #10b981;">
                    <span>↑ 20%</span>
                    <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </div>
                <div style="width: 70px; height: 25px;">
                    <svg width="70" height="25" viewBox="0 0 70 25">
                        <path d="M0,20 Q15,10 30,12 T60,5 T70,2" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="dashboard-grid two-cols" style="display: grid; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Left Panel: Applications Overview -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #1e293b; font-size: 1rem; font-weight: 700; margin: 0;">Applications Overview</h3>
                <select style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; padding: 0.4rem 0.8rem; font-size: 0.82rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer;">
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>This Year</option>
                </select>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="applicationsOverviewChart"></canvas>
            </div>
        </div>

        <!-- Right Panel: Applications by Status -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); display: flex; flex-direction: column;">
            <h3 style="color: #1e293b; font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Applications by Status</h3>
            <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                <div style="position: relative; width: 140px; height: 140px; flex-shrink: 0; margin: 0 auto;">
                    <canvas id="applicationsStatusChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <span id="doughnut-center-total" style="font-size: 1.6rem; font-weight: 700; color: #1e293b; display: block; line-height: 1;">{{ $applicationsCount }}</span>
                        <span style="font-size: 0.72rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em;">Total</span>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem; flex: 1;">
                    <!-- Legend Item: Approved -->
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #475569; font-weight: 500;">
                            <span style="width: 10px; height: 10px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                            <span>Approved</span>
                        </div>
                        <div style="color: #1e293b; font-weight: 700;">
                            {{ $approvedCount }} <span style="color: #94a3b8; font-weight: 500; font-size: 0.78rem;">({{ $applicationsCount > 0 ? round(($approvedCount / $applicationsCount) * 100, 1) : 0 }}%)</span>
                        </div>
                    </div>
                    <!-- Legend Item: Pending -->
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #475569; font-weight: 500;">
                            <span style="width: 10px; height: 10px; background: #3b82f6; border-radius: 50%; display: inline-block;"></span>
                            <span>Pending</span>
                        </div>
                        <div style="color: #1e293b; font-weight: 700;">
                            {{ $pendingCount }} <span style="color: #94a3b8; font-weight: 500; font-size: 0.78rem;">({{ $applicationsCount > 0 ? round(($pendingCount / $applicationsCount) * 100, 1) : 0 }}%)</span>
                        </div>
                    </div>
                    <!-- Legend Item: Rejected -->
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #475569; font-weight: 500;">
                            <span style="width: 10px; height: 10px; background: #94a3b8; border-radius: 50%; display: inline-block;"></span>
                            <span>Rejected</span>
                        </div>
                        <div style="color: #1e293b; font-weight: 700;">
                            {{ $rejectedCount }} <span style="color: #94a3b8; font-weight: 500; font-size: 0.78rem;">({{ $applicationsCount > 0 ? round(($rejectedCount / $applicationsCount) * 100, 1) : 0 }}%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row Section -->
    <div class="dashboard-grid two-cols" style="display: grid; gap: 1.5rem;">
        <!-- Left Panel: Recent Applications -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #1e293b; font-size: 1rem; font-weight: 700; margin: 0;">Recent Applications</h3>
                <a href="{{ route('applications.index') }}" style="color: #4f46e5; text-decoration: none; font-size: 0.85rem; font-weight: 700; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.4rem 0.8rem; background: transparent; transition: background 0.15s ease;">View All</a>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse($recentApplications as $app)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                        <div style="display: flex; align-items: center; gap: 0.85rem; min-width: 0;">
                            @php
                                $badgeBg = '#eff6ff'; $badgeColor = '#3b82f6';
                                if ($app['status'] === 'Approved') { $badgeBg = '#ecfdf5'; $badgeColor = '#10b981'; }
                                elseif ($app['status'] === 'Rejected') { $badgeBg = '#fef2f2'; $badgeColor = '#ef4444'; }
                            @endphp
                            <div style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0;">
                                <i class="bx bx-file"></i>
                            </div>
                            <div style="min-width: 0;">
                                <h4 style="color: #1e293b; font-size: 0.88rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $app['category_name'] }} Application #RCFI-2025-00{{ $app['id'] }}</h4>
                                <p style="color: #64748b; font-size: 0.78rem; margin: 0.15rem 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $app['applicant_name'] }}</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.35rem; flex-shrink: 0;">
                            <span style="display: inline-block; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                                {{ $app['status'] }}
                            </span>
                            <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">
                                {{ $app['created_at'] ? $app['created_at']->format('M d, Y h:i A') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem 0; color: #94a3b8; font-size: 0.88rem; font-weight: 500;">
                        No applications found in database.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Panel: Quick Actions -->
        <div class="panel-premium" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.02); display: flex; flex-direction: column;">
            <h3 style="color: #1e293b; font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Quick Actions</h3>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; flex: 1;">
                <!-- Action 1: Add New Application -->
                <a href="{{ route('applications.index') }}" class="quick-action-card" style="display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; text-decoration: none; color: inherit; background: #ffffff; transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;">
                    <div style="background: #f5f3ff; color: #8b5cf6; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="bx bx-file-blank"></i>
                    </div>
                    <div style="margin-top: 1.25rem;">
                        <h4 style="color: #1e293b; font-size: 0.84rem; font-weight: 700; margin: 0;">Add New Application</h4>
                        <p style="color: #94a3b8; font-size: 0.72rem; margin: 0.15rem 0 0; font-weight: 500;">Create a new application</p>
                    </div>
                </a>

                <!-- Action 2: Add New Staff -->
                <a href="{{ route('users') }}" class="quick-action-card" style="display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; text-decoration: none; color: inherit; background: #ffffff; transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;">
                    <div style="background: #eff6ff; color: #3b82f6; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="bx bx-user-plus"></i>
                    </div>
                    <div style="margin-top: 1.25rem;">
                        <h4 style="color: #1e293b; font-size: 0.84rem; font-weight: 700; margin: 0;">Add New Staff</h4>
                        <p style="color: #94a3b8; font-size: 0.72rem; margin: 0.15rem 0 0; font-weight: 500;">Register a new staff member</p>
                    </div>
                </a>

                <!-- Action 3: Add New Project -->
                <a href="{{ route('projects.index') }}" class="quick-action-card" style="display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; text-decoration: none; color: inherit; background: #ffffff; transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;">
                    <div style="background: #ecfdf5; color: #10b981; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="bx bx-briefcase-alt-2"></i>
                    </div>
                    <div style="margin-top: 1.25rem;">
                        <h4 style="color: #1e293b; font-size: 0.84rem; font-weight: 700; margin: 0;">Add New Project</h4>
                        <p style="color: #94a3b8; font-size: 0.72rem; margin: 0.15rem 0 0; font-weight: 500;">Create a new project</p>
                    </div>
                </a>

                <!-- Action 4: Generate Report -->
                <a href="{{ route('applications.index') }}" class="quick-action-card" style="display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; text-decoration: none; color: inherit; background: #ffffff; transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;">
                    <div style="background: #fff7ed; color: #f97316; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="bx bx-bar-chart-alt-2"></i>
                    </div>
                    <div style="margin-top: 1.25rem;">
                        <h4 style="color: #1e293b; font-size: 0.84rem; font-weight: 700; margin: 0;">Generate Report</h4>
                        <p style="color: #94a3b8; font-size: 0.72rem; margin: 0.15rem 0 0; font-weight: 500;">Download reports</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fontFamily = getComputedStyle(document.body).fontFamily || 'Inter, sans-serif';
        Chart.defaults.font.family = fontFamily;
        Chart.defaults.color = '#64748b';

        // Line Chart: Applications Overview
        new Chart(document.getElementById('applicationsOverviewChart'), {
            type: 'line',
            data: {
                labels: ['May 1', 'May 7', 'May 13', 'May 19', 'May 25', 'May 31'],
                datasets: [{
                    label: 'Applications',
                    data: [3, 8, 7, 12, 6, 15],
                    borderColor: '#6366f1',
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
                        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.00)');
                        return gradient;
                    },
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        border: { dash: [4, 4] }
                    }
                }
            }
        });

        // Doughnut Chart: Applications by Status
        new Chart(document.getElementById('applicationsStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [{{ $approvedCount }}, {{ $pendingCount }}, {{ $rejectedCount }}],
                    backgroundColor: ['#10b981', '#3b82f6', '#94a3b8'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>

@endsection
