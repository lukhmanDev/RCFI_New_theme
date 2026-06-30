@extends('layouts.admin')

@section('title', 'Projects Dashboard')

@section('content')

    <style>
        .group-container {
            margin-bottom: 2.5rem;
            width: 100%;
        }
        .group-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: #38bdf8; /* Sleek blue heading */
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid rgba(56, 189, 248, 0.2);
            padding-bottom: 0.5rem;
        }
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .project-card {
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            color: #ffffff;
            min-height: 160px;
            text-decoration: none;
            background: linear-gradient(135deg, #10b981, #059669); /* Elegant emerald green matching mockup */
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.5);
            filter: brightness(1.1);
        }
        .project-card-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .project-card-icon-container {
            width: 48px;
            height: 48px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .project-card-info h5 {
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.9);
            margin: 0 0 0.25rem 0;
            font-weight: 700;
        }
        .project-card-info h4 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }
        .project-card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 0.75rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
        }
    </style>

    <div style="margin-bottom: 2rem;">
        <h2 class="panel-title" style="font-size: 1.5rem; color: #ffffff;">Projects Dashboard</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Select a category card to manage registered projects for each application.</p>
    </div>

    <!-- Groups loop matching the category setup -->
    @foreach($groupedCategories as $groupName => $cats)
        <div class="group-container">
            <h3 class="group-header">{{ $groupName }}</h3>
            <div class="projects-grid">
                @foreach($cats as $slug => $config)
                    @php
                        $count = $counts[$config['name']] ?? 0;
                    @endphp
                    <a href="{{ route('projects.category', $slug) }}" class="project-card" style="background: {{ $config['bg'] }};">
                        <div class="project-card-top">
                            <div class="project-card-icon-container">
                                <i class="{{ $config['icon'] }}"></i>
                            </div>
                            <div class="project-card-info">
                                <h5>{{ $config['name'] }}</h5>
                                <h4>{{ $count }}</h4>
                            </div>
                        </div>
                        <div class="project-card-bottom">
                            <span>View Projects</span>
                            <i class="bx bx-right-arrow-alt" style="font-size: 1.25rem;"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

@endsection
