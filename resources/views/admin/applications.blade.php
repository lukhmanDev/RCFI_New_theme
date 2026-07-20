@extends('layouts.admin')

@section('title', 'Applications Categories')

@section('content')

    <style>
        .app-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            width: 100%;
        }
        .app-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01);
            color: var(--text-main);
            min-height: 155px;
            text-decoration: none;
            position: relative;
        }
        .app-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .app-card-top {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }
        .app-card-info {
            padding-right: 80px;
        }
        .app-card-info h5 {
            font-size: 0.76rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #64748b;
            margin: 0 0 0.25rem 0;
            font-weight: 700;
        }
        .app-card-info h4 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .app-card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.85rem;
            font-size: 0.84rem;
            color: #4f46e5;
            font-weight: 600;
            transition: color 0.15s ease;
        }
        .app-card:hover .app-card-bottom {
            color: #312e81;
        }
    </style>

    <div style="margin-bottom: 2rem;">
        <h1 style="color: #1e293b; font-size: 1.75rem; font-weight: 700; margin: 0;">Applications Dashboard</h1>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 0.25rem;">Select a category card to manage registered applications.</p>
    </div>

    @foreach($groupedCategories as $groupTitle => $cats)
        <!-- Group Section Divider Header -->
        <div style="margin-top: 2.25rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <span style="color: #4f46e5; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">{{ $groupTitle }}</span>
            <div style="flex: 1; height: 1px; background-color: #e2e8f0;"></div>
        </div>

        <div class="app-grid">
            @foreach($cats as $slug => $config)
                @php
                    $count = $counts[$config['name']] ?? 0;
                    $pendingCount = $pendingCounts[$config['name']] ?? 0;
                @endphp
                <a href="{{ route('applications.category', $slug) }}" class="app-card">
                    @if($pendingCount > 0)
                        <!-- Red dot and pending count badge on the right side -->
                        <div style="position: absolute; top: 1rem; right: 1rem; background-color: rgba(239, 68, 68, 0.05); color: #ef4444; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; display: flex; align-items: center; gap: 0.3rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                            <span style="width: 5px; height: 5px; background-color: #ef4444; border-radius: 50%; display: inline-block;"></span>
                            {{ $pendingCount }} Pending
                        </div>
                    @endif
                    <div class="app-card-top">
                        <div class="app-card-info">
                            <h5>{{ $config['name'] }}</h5>
                            <h4>{{ $approvedProjectCounts[$config['name']] ?? 0 }} <span style="font-size: 0.9rem; font-weight: 500; color: #94a3b8;">/ {{ $totalProjectCounts[$config['name']] ?? 0 }}</span></h4>
                        </div>
                    </div>
                    <div class="app-card-bottom">
                        <span>View Applications</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endforeach

@endsection
