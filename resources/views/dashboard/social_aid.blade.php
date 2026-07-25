@extends('layouts.admin')

@section('title', 'Social Aid Manager Dashboard')

@section('content')

    <!-- Welcoming Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="color: var(--text-main); font-size: 1.75rem; font-weight: 700; margin: 0;">Welcome, {{ Auth::user()->name }}!</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Role assigned: 
            <span style="color: #10b981; font-weight: 600;">Social Aid Manager</span>
        </p>
    </div>

    <!-- Stats overview grid -->
    <div class="stats-grid" style="margin-bottom: 2.5rem;">
        <!-- Approved Applications Card -->
        <div class="stat-card">
            <div class="stat-details">
                <h3>Approved Applications</h3>
                <p>{{ $approvedCount }}</p>
            </div>
            <div class="stat-icon green">
                <i class="bx bxs-check-circle"></i>
            </div>
        </div>

        <!-- Total Applications Card -->
        <div class="stat-card">
            <div class="stat-details">
                <h3>Total Social Aid Applications</h3>
                <p>{{ $applicationsCount }}</p>
            </div>
            <div class="stat-icon blue">
                <i class="bx bxs-file-doc"></i>
            </div>
        </div>

        <!-- Pending Applications Card -->
        <div class="stat-card">
            <div class="stat-details">
                <h3>Pending Applications</h3>
                <p>{{ $pendingCount }}</p>
            </div>
            <div class="stat-icon orange">
                <i class="bx bxs-time"></i>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Cards -->
    <div class="panel" style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 1.25rem;">SOCIAL AID PROJECTS & CARE</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <!-- Orphan Care -->
            <a href="{{ route('projects.category', 'orphan-care') }}" style="text-decoration: none;">
                <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 10px; padding: 1.25rem; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="bx bxs-heart"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-main);">Orphan Care</h3>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Manage Orphan Care Projects</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Differently Abled -->
            <a href="{{ route('projects.category', 'differently-abled') }}" style="text-decoration: none;">
                <div style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 10px; padding: 1.25rem; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="bx bx-accessibility"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-main);">Differently Abled</h3>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Manage Differently Abled Projects</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Family Aid -->
            <a href="{{ route('projects.category', 'family-aid') }}" style="text-decoration: none;">
                <div style="background: rgba(168, 85, 247, 0.08); border: 1px solid rgba(168, 85, 247, 0.2); border-radius: 10px; padding: 1.25rem; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: #a855f7; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="bx bxs-group"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-main);">Family Aid</h3>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Manage Family Aid Projects</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

@endsection
