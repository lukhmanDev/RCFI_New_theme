@extends('layouts.admin')

@section('title', 'Approved Applications')

@section('content')

    @php
        $groupedCategoryConfigs = [
            'Construction Applications' => [
                'education-center' => [
                    'name' => 'Education Center',
                    'slug' => 'education-center'
                ],
                'cultural-center' => [
                    'name' => 'Cultural Center',
                    'slug' => 'cultural-center'
                ],
                'hospital-or-clinics' => [
                    'name' => 'Hospital or Clinics',
                    'slug' => 'hospital-or-clinics'
                ],
                'shops-and-others' => [
                    'name' => 'Shops and Others',
                    'slug' => 'shops-and-others'
                ],
                'house' => [
                    'name' => 'House',
                    'slug' => 'house'
                ]
            ],
            'Drinking Water Applications' => [
                'drinking-water-group-level' => [
                    'name' => 'Drinking Water - Group Level',
                    'slug' => 'drinking-water-group-level'
                ],
                'drinking-water-individual-level' => [
                    'name' => 'Drinking Water - Individual Level',
                    'slug' => 'drinking-water-individual-level'
                ]
            ],
            'Social Aid & Care' => [
                'orphan-care' => [
                    'name' => 'Orphan Care',
                    'slug' => 'orphan-care'
                ],
                'differently-abled' => [
                    'name' => 'Differently Abled',
                    'slug' => 'differently-abled'
                ],
                'family-aid' => [
                    'name' => 'Family Aid',
                    'slug' => 'family-aid'
                ]
            ],
            'General Schemes' => [
                'general' => [
                    'name' => 'General',
                    'slug' => 'general'
                ]
            ]
        ];
    @endphp

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
            color: #4f46e5;
            font-weight: 600;
            transition: color 0.15s ease;
        }
        .app-card:hover .app-card-bottom {
            color: #312e81;
        }
    </style>

    <div style="margin-bottom: 2rem;">
        <h1 style="color: #1e293b; font-size: 1.75rem; font-weight: 700; margin: 0;">Approved Applications Dashboard</h1>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 0.25rem;">Select a category card to view approved application projects.</p>
    </div>

    <!-- Grouped Cards Grid -->
    @foreach($groupedCategoryConfigs as $groupTitle => $configs)
        <!-- Group Divider Header -->
        <div style="margin-top: 2.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
            <span style="color: #4f46e5; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">{{ $groupTitle }}</span>
            <div style="flex: 1; height: 1px; background-color: #e2e8f0;"></div>
        </div>

        <div class="app-grid">
            @foreach($configs as $slug => $config)
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
