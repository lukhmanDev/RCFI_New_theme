@extends('layouts.admin')

@section('title', 'Applications Categories')

@section('content')

    @php
        $groupedCategoryConfigs = [
            'Construction Applications' => [
                'education-center' => [
                    'name' => 'Education Center',
                    'icon' => 'bx bxs-graduation',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ],
                'cultural-center' => [
                    'name' => 'Cultural Center',
                    'icon' => 'bx bxs-landmark',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ],
                'hospital-or-clinics' => [
                    'name' => 'Hospital or Clinics',
                    'icon' => 'bx bxs-plus-medical',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ],
                'shops-and-others' => [
                    'name' => 'Shops and Others',
                    'icon' => 'bx bxs-store-alt',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ],
                'house' => [
                    'name' => 'House',
                    'icon' => 'bx bxs-home',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ]
            ],
            'Drinking Water Applications' => [
                'drinking-water-group-level' => [
                    'name' => 'Drinking Water - Group Level',
                    'icon' => 'bx bx-water',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ],
                'drinking-water-individual-level' => [
                    'name' => 'Drinking Water - Individual Level',
                    'icon' => 'bx bxs-droplet',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ]
            ],
            'Social Aid & Care' => [
                'orphan-care' => [
                    'name' => 'Orphan Care',
                    'icon' => 'bx bxs-face',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ],
                'differently-abled' => [
                    'name' => 'Differently Abled',
                    'icon' => 'bx bx-accessibility',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ],
                'family-aid' => [
                    'name' => 'Family Aid',
                    'icon' => 'bx bxs-group',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
                ]
            ],
            'General Schemes' => [
                'general' => [
                    'name' => 'General',
                    'icon' => 'bx bxs-file-blank',
                    'bg' => 'linear-gradient(135deg, #10b981, #059669)'
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
        <h2 class="panel-title" style="font-size: 1.5rem; color: #ffffff;">Applications Dashboard</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Select a category card to manage registered applications.</p>
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
                    $count = $counts[$config['name']] ?? 0;
                    $pendingCount = $pendingCounts[$config['name']] ?? 0;
                @endphp
                 <a href="{{ route('applications.category', $slug) }}" class="app-card" style="background: {{ $config['bg'] }}; position: relative;">
                    @if($pendingCount > 0)
                        <!-- Red dot and pending count badge on the right side -->
                        <div style="position: absolute; top: 0.75rem; right: 0.75rem; background-color: var(--accent-red); color: #ffffff; padding: 0.2rem 0.45rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 700; display: flex; align-items: center; gap: 0.25rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.15);">
                            <span style="width: 5px; height: 5px; background-color: #ffffff; border-radius: 50%; display: inline-block; box-shadow: 0 0 2px #ffffff;"></span>
                            {{ $pendingCount }} Pending
                        </div>
                    @endif
                    <div class="app-card-top">
                        <div class="app-card-icon-container">
                            <i class="{{ $config['icon'] }}"></i>
                        </div>
                        <div class="app-card-info" style="padding-right: 75px;">
                            <h5>{{ $config['name'] }}</h5>
                            <h4>{{ $approvedProjectCounts[$config['name']] ?? 0 }} <span style="font-size: 0.95rem; font-weight: 500; opacity: 0.75;">/ {{ $totalProjectCounts[$config['name']] ?? 0 }}</span></h4>
                        </div>
                    </div>
                    <div class="app-card-bottom">
                        <span>View Applications</span>
                        <i class="bx bx-right-arrow-alt" style="font-size: 1.25rem;"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endforeach

@endsection
