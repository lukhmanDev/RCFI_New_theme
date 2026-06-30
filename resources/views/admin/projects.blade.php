@extends('layouts.admin')

@section('title', 'Projects')

@section('content')

    <style>
        /* Progressively hide columns from right-to-left as screen size decreases, keeping Application ID, Project, Applicant Name and Actions always visible */
        @media (max-width: 1200px) {
            .col-amount { display: none !important; }
        }
        @media (max-width: 900px) {
            .col-status { display: none !important; }
        }
    </style>

    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title">All Projects / Applications</h2>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Project</th>
                        <th>Name of Applicant</th>
                        <th class="col-amount" style="text-align: center;">Amount Requested</th>
                        <th class="col-status" style="text-align: center;">Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            // Get prefixes and build application ID
                            $prefixes = [
                                'Education Center' => 'EC',
                                'Cultural Center' => 'CC',
                                'Hospital or Clinics' => 'HC',
                                'Shops and Others' => 'SO',
                                'House' => 'HS',
                                'Drinking Water - Group Level' => 'DWG',
                                'Drinking Water - Individual Level' => 'DWI',
                                'Orphan Care' => 'OC',
                                'Differently Abled' => 'DA',
                                'Family Aid' => 'FA',
                                'General' => 'GN'
                            ];
                            $prefix = $prefixes[$appItem->project_type] ?? 'APP';
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . $prefix . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <!-- Application ID -->
                            <td style="font-weight: 600; color: var(--accent-cyan);">
                                {{ $appId }}
                            </td>

                            <!-- Project Type (Separate column as requested!) -->
                            <td style="font-weight: 600; color: #ffffff;">
                                <span style="background-color: rgba(16, 185, 129, 0.15); color: var(--accent-green); padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; border: 1px solid rgba(16, 185, 129, 0.25);">
                                    {{ $appItem->project_type }}
                                </span>
                            </td>

                            <!-- Name of Applicant -->
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>

                            <!-- Amount Requested -->
                            <td class="col-amount" style="text-align: center;">
                                {{ $appItem->amount_requested ? '₹' . number_format($appItem->amount_requested) : 'N/A' }}
                            </td>

                            <!-- Status -->
                            <td class="col-status" style="text-align: center;">
                                @php
                                    $statusColors = [
                                        'Pending' => ['bg' => 'rgba(245, 158, 11, 0.15)', 'text' => '#f59e0b', 'border' => 'rgba(245, 158, 11, 0.25)'],
                                        'Approved' => ['bg' => 'rgba(16, 185, 129, 0.15)', 'text' => 'var(--accent-green)', 'border' => 'rgba(16, 185, 129, 0.25)'],
                                        'Rejected' => ['bg' => 'rgba(239, 68, 68, 0.15)', 'text' => '#ef4444', 'border' => 'rgba(239, 68, 68, 0.25)'],
                                    ];
                                    $style = $statusColors[$appItem->status] ?? ['bg' => 'rgba(156, 163, 175, 0.15)', 'text' => 'var(--text-muted)', 'border' => 'rgba(156, 163, 175, 0.25)'];
                                @endphp
                                <span style="background-color: {{ $style['bg'] }}; color: {{ $style['text'] }}; border: 1px solid {{ $style['border'] }}; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $appItem->status }}
                                </span>
                            </td>

                            <!-- Action -->
                            <td style="text-align: center; white-space: nowrap;">
                                <a href="{{ route('applications.category', $appItem->category_slug) }}" class="btn-custom" style="text-decoration: none; display: inline-block; background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                                    View Category
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No projects or applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($applications->hasPages())
            <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

@endsection
