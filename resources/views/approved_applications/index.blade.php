@extends('layouts.admin')

@section('title', 'Approved Applications')

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
            min-height: 145px;
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
        .app-card-info h5 {
            font-size: 0.76rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #64748b;
            margin: 0;
            font-weight: 700;
        }
        .app-card-info h4 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0.25rem 0 0;
        }
        .app-card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.85rem;
            font-size: 0.84rem;
            color: #10b981;
            font-weight: 600;
            transition: color 0.15s ease;
        }
        .app-card:hover .app-card-bottom {
            color: #059669;
        }
    </style>

    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #1e293b; font-size: 1.75rem; font-weight: 700; margin: 0;">Approved Applications Dashboard</h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 0.25rem;">Select a category card to view approved application projects.</p>
        </div>
        <div>
            <a href="{{ route('applications.approved.export', ['category' => 'all']) }}" onclick="event.preventDefault(); const url = this.href; window.location.href = url; setTimeout(function(){ window.location.reload(); }, 1500);" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; padding: 0.65rem 1.25rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);" title="Download Excel report with all approved application data">
                <i class="bx bxs-file-export" style="font-size: 1.15rem;"></i> Export All Approved Excel
            </a>
        </div>
    </div>

    @foreach($groupedCategories as $groupTitle => $cats)
        <!-- Group Section Divider Header -->
        <div style="margin-top: 2.25rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <span style="color: #10b981; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">{{ $groupTitle }}</span>
            <div style="flex: 1; height: 1px; background-color: #e2e8f0;"></div>
        </div>

        <div class="app-grid">
            @foreach($cats as $slug => $config)
                @php
                    $count = $approvedCounts[$config['name']] ?? 0;
                @endphp
                <a href="{{ route('applications.approved.category', $slug) }}" class="app-card">
                    <div class="app-card-top">
                        <div class="app-card-info">
                            <h5>{{ $config['name'] }}</h5>
                            <h4>{{ $count }}</h4>
                        </div>
                    </div>
                    <div class="app-card-bottom">
                        <span>View Approved Registry</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endforeach

@endsection
