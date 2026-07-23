@extends('layouts.admin')

@section('title', 'Reception Dashboard')

@section('content')

    <!-- Welcoming Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: var(--text-main); font-size: 1.75rem; font-weight: 700; margin: 0;">Welcome, {{ Auth::user()->name }}!</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">
                Role assigned: <span style="color: var(--accent-cyan); font-weight: 600;">Reception Desk</span>
            </p>
        </div>
        <div>
            <a href="{{ route('applications.index') }}" class="btn-custom" style="padding: 0.65rem 1.25rem; font-size: 0.9rem; border-radius: 10px; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="bx bxs-file-doc"></i> View All Applications
            </a>
        </div>
    </div>

    <!-- Stats Overview Grid -->
    <div class="stats-grid" style="margin-bottom: 2.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <!-- Total Applications Card -->
        <div class="stat-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <div class="stat-details">
                <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 600; margin: 0 0 0.4rem 0;">Total Applications</h3>
                <p style="color: #1e293b; font-size: 1.6rem; font-weight: 700; margin: 0;">{{ $applicationsCount }}</p>
            </div>
            <div class="stat-icon green" style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bx bxs-file-doc"></i>
            </div>
        </div>

        <!-- Pending Applications Card -->
        <div class="stat-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <div class="stat-details">
                <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 600; margin: 0 0 0.4rem 0;">Pending Verification</h3>
                <p style="color: #1e293b; font-size: 1.6rem; font-weight: 700; margin: 0;">{{ $pendingCount }}</p>
            </div>
            <div class="stat-icon orange" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bx bxs-time-five"></i>
            </div>
        </div>

        <!-- Category Hub Card -->
        <div class="stat-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <div class="stat-details">
                <h3 style="color: #64748b; font-size: 0.85rem; font-weight: 600; margin: 0 0 0.4rem 0;">Available Categories</h3>
                <p style="color: #1e293b; font-size: 1.6rem; font-weight: 700; margin: 0;">11 Categories</p>
            </div>
            <div class="stat-icon purple" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bx bxs-category"></i>
            </div>
        </div>
    </div>

    <!-- Quick Application Categories Entry Grid -->
    <div class="panel" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; margin-bottom: 2.5rem;">
        <div class="panel-header" style="margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
            <h2 class="panel-title" style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bx bx-plus-circle" style="color: #4f46e5;"></i> Application Entry & Management
            </h2>
            <p style="color: #64748b; font-size: 0.85rem; margin: 0.25rem 0 0 0;">Select a category to add new applications, view records, edit, or delete.</p>
        </div>

        @php
            $receptionCategories = [
                ['slug' => 'education-center', 'name' => 'Education Center', 'icon' => 'bx bxs-graduation', 'color' => '#10b981'],
                ['slug' => 'cultural-center', 'name' => 'Cultural Center', 'icon' => 'bx bxs-landmark', 'color' => '#3b82f6'],
                ['slug' => 'hospital-or-clinics', 'name' => 'Hospital & Clinics', 'icon' => 'bx bxs-plus-medical', 'color' => '#ef4444'],
                ['slug' => 'shops-and-others', 'name' => 'Shops and Others', 'icon' => 'bx bxs-store-alt', 'color' => '#f59e0b'],
                ['slug' => 'house', 'name' => 'House Project', 'icon' => 'bx bxs-home', 'color' => '#8b5cf6'],
                ['slug' => 'drinking-water-group-level', 'name' => 'Drinking Water (Group)', 'icon' => 'bx bxs-droplet', 'color' => '#06b6d4'],
                ['slug' => 'drinking-water-individual-level', 'name' => 'Drinking Water (Individual)', 'icon' => 'bx bxs-water', 'color' => '#0284c7'],
                ['slug' => 'orphan-care', 'name' => 'Orphan Care', 'icon' => 'bx bxs-heart', 'color' => '#ec4899'],
                ['slug' => 'differently-abled', 'name' => 'Differently Abled', 'icon' => 'bx bxs-user-check', 'color' => '#14b8a6'],
                ['slug' => 'family-aid', 'name' => 'Family Aid', 'icon' => 'bx bxs-group', 'color' => '#6366f1'],
                ['slug' => 'general', 'name' => 'General Application', 'icon' => 'bx bxs-folder-open', 'color' => '#64748b'],
            ];
        @endphp

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 1rem;">
            @foreach($receptionCategories as $cat)
                <a href="{{ route('applications.category', $cat['slug']) }}" style="display: flex; align-items: center; gap: 0.85rem; padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.borderColor='{{ $cat['color'] }}'; this.style.background='#ffffff'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.transform='none';">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $cat['color'] }}15; color: {{ $cat['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <i class="{{ $cat['icon'] }}"></i>
                    </div>
                    <div style="min-width: 0;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $cat['name'] }}</div>
                        <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.15rem; font-weight: 500;">Add & Manage &rarr;</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="panel" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;">
        <div class="panel-header" style="margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title" style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;">Recent Registered Applications</h2>
            <a href="{{ route('applications.index') }}" style="color: #4f46e5; font-size: 0.85rem; font-weight: 600; text-decoration: none;">View All &rarr;</a>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0; text-align: left;">
                        <th style="padding: 0.75rem; font-size: 0.85rem; color: #64748b;">Applicant Name</th>
                        <th style="padding: 0.75rem; font-size: 0.85rem; color: #64748b;">Category</th>
                        <th style="padding: 0.75rem; font-size: 0.85rem; color: #64748b;">Date Registered</th>
                        <th style="padding: 0.75rem; font-size: 0.85rem; color: #64748b; text-align: center;">Status</th>
                        <th style="padding: 0.75rem; font-size: 0.85rem; color: #64748b; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentApplications as $recent)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.85rem 0.75rem; font-weight: 700; color: #1e293b;">
                                {{ $recent['applicant_name'] ?? 'N/A' }}
                            </td>
                            <td style="padding: 0.85rem 0.75rem; color: #475569; font-weight: 500;">
                                {{ $recent['category_name'] ?? 'General' }}
                            </td>
                            <td style="padding: 0.85rem 0.75rem; color: #64748b; font-size: 0.85rem;">
                                {{ !empty($recent['created_at']) ? $recent['created_at']->format('M d, Y') : 'N/A' }}
                            </td>
                            <td style="padding: 0.85rem 0.75rem; text-align: center;">
                                @php
                                    $st = $recent['status'] ?? 'Pending';
                                    $stColor = $st === 'Approved' ? '#10b981' : ($st === 'Rejected' ? '#ef4444' : '#f59e0b');
                                @endphp
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700; font-size: 0.8rem; color: {{ $stColor }};">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $stColor }};"></span>
                                    {{ $st }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 0.75rem; text-align: center;">
                                <a href="{{ route('applications.category', $recent['category']) }}" style="color: #3b82f6; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.3rem 0.6rem; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class="bx bx-right-arrow-alt"></i> Open Category
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">No recent applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
