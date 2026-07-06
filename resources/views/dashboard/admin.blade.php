@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

    <!-- Welcoming Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="color: #ffffff; font-size: 1.75rem; font-weight: 700; margin: 0;">Welcome, {{ Auth::user()->name }}!</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">Role assigned: 
            <span style="color: var(--accent-green); font-weight: 600;">System Administrator</span>
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="margin-bottom: 2.5rem;">
        <!-- Registered Users -->
        <div class="stat-card">
            <div class="stat-details">
                <h3>Total Registered Users</h3>
                <p>{{ $userCount }}</p>
            </div>
            <div class="stat-icon cyan">
                <i class="bx bxs-group"></i>
            </div>
        </div>
        
        <!-- Registered Donors / Partners -->
        <div class="stat-card">
            <div class="stat-details">
                <h3>Registered Partners</h3>
                <p>{{ $donorsCount }}</p>
            </div>
            <div class="stat-icon purple">
                <i class="bx bxs-business"></i>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="stat-card">
            <div class="stat-details">
                <h3>Total Applications</h3>
                <p>{{ $applicationsCount }}</p>
            </div>
            <div class="stat-icon green">
                <i class="bx bxs-file-doc"></i>
            </div>
        </div>
    </div>

    
@endsection
