@extends('layouts.admin')

@section('title', 'Approved Applications')

@section('content')

    @php
        $groupedCategoryConfigs = [
            'Construction Applications' => [
                'education-center' => [
                    'name' => 'Education Center',
                    'icon' => 'bx bxs-graduation',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ],
                'cultural-center' => [
                    'name' => 'Cultural Center',
                    'icon' => 'bx bxs-landmark',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ],
                'hospital-or-clinics' => [
                    'name' => 'Hospital or Clinics',
                    'icon' => 'bx bxs-plus-medical',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ],
                'shops-and-others' => [
                    'name' => 'Shops and Others',
                    'icon' => 'bx bxs-store-alt',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ],
                'house' => [
                    'name' => 'House',
                    'icon' => 'bx bxs-home',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ]
            ],
            'Drinking Water Applications' => [
                'drinking-water-group-level' => [
                    'name' => 'Drinking Water - Group Level',
                    'icon' => 'bx bx-water',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ],
                'drinking-water-individual-level' => [
                    'name' => 'Drinking Water - Individual Level',
                    'icon' => 'bx bxs-droplet',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ]
            ],
            'Social Aid & Care' => [
                'orphan-care' => [
                    'name' => 'Orphan Care',
                    'icon' => 'bx bxs-face',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ],
                'differently-abled' => [
                    'name' => 'Differently Abled',
                    'icon' => 'bx bx-accessibility',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ],
                'family-aid' => [
                    'name' => 'Family Aid',
                    'icon' => 'bx bxs-group',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
                ]
            ],
            'General Schemes' => [
                'general' => [
                    'name' => 'General',
                    'icon' => 'bx bxs-file-blank',
                    'bg' => 'linear-gradient(135deg, #059669, #047857)'
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
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            color: #ffffff;
            min-height: 150px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.5);
            filter: brightness(1.1);
        }
        .app-card-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .app-card-icon-container {
            width: 48px;
            height: 48px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .app-card-info h5 {
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.85);
            margin: 0 0 0.25rem 0;
            font-weight: 700;
        }
        .app-card-info h4 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }
        .app-card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 0.75rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
        }
    </style>

    <div style="margin-bottom: 2rem;">
        <h2 class="panel-title" style="font-size: 1.5rem; color: #ffffff;">Approved Applications Dashboard</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Select a category card to view approved application projects.</p>
    </div>

    <!-- Grouped Cards Grid -->
    @foreach($groupedCategoryConfigs as $groupTitle => $configs)
        <!-- Group Divider Header -->
        <div style="margin-top: 2.5rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem;">
            <span style="color: var(--accent-cyan); font-weight: 700; font-size: 0.85rem; letter-spacing: 0.05em; text-transform: uppercase;">{{ $groupTitle }}</span>
            <div style="flex: 1; height: 1px; background-color: rgba(255,255,255,0.08);"></div>
        </div>

        <div class="app-grid">
            @foreach($configs as $slug => $config)
                @php
                    $count = $approvedCounts[$config['name']] ?? 0;
                @endphp
                <a href="{{ route('applications.approved.category', $slug) }}" class="app-card" style="background: {{ $config['bg'] }};">
                    <div class="app-card-top">
                        <div class="app-card-icon-container">
                            <i class="{{ $config['icon'] }}"></i>
                        </div>
                        <div class="app-card-info">
                            <h5>{{ $config['name'] }}</h5>
                            <h4>{{ $count }}</h4>
                        </div>
                    </div>
                    <div class="app-card-bottom">
                        <span>View Approved Registry</span>
                        <i class="bx bx-right-arrow-alt" style="font-size: 1.25rem;"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endforeach

@endsection
