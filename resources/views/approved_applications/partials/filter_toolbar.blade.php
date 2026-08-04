@php
    $isSocialAid = in_array($categorySlug ?? '', ['orphan-care', 'differently-abled', 'family-aid']);
@endphp

<div style="margin-bottom: 1.25rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
    <form method="GET" action="{{ url()->current() }}" id="approvedFilterForm" style="margin: 0;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.85rem; align-items: flex-end; justify-content: space-between;">
            
            <!-- Filter Fields Grid -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; flex: 1;">
                
                @if($isSocialAid)
                    <!-- Sponsor Status Filter (ONLY for Social Aid Projects) -->
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Sponsor Status</label>
                        <select name="sponsor_status" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none;">
                            <option value="all">All Status</option>
                            <option value="sponsored" {{ request('sponsor_status') === 'sponsored' ? 'selected' : '' }}>Sponsored</option>
                            <option value="not sponsored" {{ in_array(request('sponsor_status'), ['not sponsored', 'un-sponsored', 'unsponsored']) ? 'selected' : '' }}>Not Sponsored</option>
                        </select>
                    </div>

                    <!-- Cluster Filter (ONLY for Social Aid Projects) -->
                    @if(!empty($clusters) && count($clusters) > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Cluster</label>
                        <select name="cluster_id" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none;">
                            <option value="all">All Clusters</option>
                            @foreach($clusters as $c)
                                <option value="{{ $c->id }}" {{ (string)request('cluster_id') === (string)$c->id ? 'selected' : '' }}>
                                    [{{ $c->code }}] {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                @else
                    <!-- Project Manager Filter (For Non-Social Aid Projects) -->
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Project Manager</label>
                        <select name="pm_id" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none; max-width: 170px;">
                            <option value="all">All Managers</option>
                            @if(!empty($projectManagers))
                                @foreach($projectManagers as $pm)
                                    <option value="{{ $pm->id }}" {{ (string)request('pm_id') === (string)$pm->id ? 'selected' : '' }}>
                                        {{ $pm->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                @endif

                <!-- Agency Filter -->
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Agency</label>
                    <select name="agency" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none; max-width: 160px;">
                        <option value="all">All Agencies</option>
                        @if(!empty($agencies))
                            @foreach($agencies as $ag)
                                <option value="{{ $ag }}" {{ request('agency') === $ag ? 'selected' : '' }}>
                                    {{ $ag }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- State Filter -->
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">State</label>
                    <select name="state" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none; max-width: 150px;">
                        <option value="all">All States</option>
                        @if(!empty($states))
                            @foreach($states as $st)
                                <option value="{{ $st }}" {{ request('state') === $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- District Filter -->
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">District</label>
                    <select name="district" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none; max-width: 150px;">
                        <option value="all">All Districts</option>
                        @if(!empty($districts))
                            @foreach($districts as $dt)
                                <option value="{{ $dt }}" {{ request('district') === $dt ? 'selected' : '' }}>
                                    {{ $dt }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                @if(!$isSocialAid)
                    <!-- Type of Project Filter (For Non-Social Aid Projects) -->
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Type of Project</label>
                        <select name="type_of_project" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none; max-width: 160px;">
                            <option value="all">All Types</option>
                            @if(!empty($projectTypes))
                                @foreach($projectTypes as $pt)
                                    <option value="{{ $pt }}" {{ request('type_of_project') === $pt ? 'selected' : '' }}>
                                        {{ $pt }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Running Project / Project Status Filter (For Non-Social Aid Projects) -->
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">Running Project</label>
                        <select name="running_project" onchange="this.form.submit();" style="height: 36px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.825rem; font-weight: 500; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; outline: none; min-width: 140px;">
                            <option value="all">All Statuses</option>
                            <option value="completed" {{ request('running_project') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="running" {{ request('running_project') === 'running' ? 'selected' : '' }}>Running</option>
                            <option value="not_set" {{ in_array(request('running_project'), ['not_set', 'not set']) ? 'selected' : '' }}>Not set</option>
                        </select>
                    </div>
                @endif

                <!-- Reset Filters Button if any filter active -->
                @if(request()->hasAny(['sponsor_status', 'pm_id', 'agency', 'state', 'district', 'type_of_project', 'running_project', 'cluster_id']) && 
                    (request('sponsor_status', 'all') !== 'all' || request('pm_id', 'all') !== 'all' || request('agency', 'all') !== 'all' || request('state', 'all') !== 'all' || request('district', 'all') !== 'all' || request('type_of_project', 'all') !== 'all' || request('running_project', 'all') !== 'all' || request('cluster_id', 'all') !== 'all'))
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <a href="{{ url()->current() }}" class="btn-custom" style="height: 36px; background: #ef4444; color: #ffffff; padding: 0 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;" title="Reset all filters">
                            <i class="bx bx-refresh"></i> Reset
                        </a>
                    </div>
                @endif

            </div>

            <!-- Search & Export Excel Toolbar -->
            <div style="display: flex; gap: 0.75rem; align-items: center; margin-top: 0.25rem;">
                <div style="position: relative; width: 100%; min-width: 180px; max-width: 220px;">
                    <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; pointer-events: none;"><i class="bx bx-search"></i></span>
                    <input type="text" id="tableSearchInput" placeholder="Search Applications..." style="width: 100%; height: 36px; padding: 0 0.85rem 0 2.25rem; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; font-size: 0.85rem; outline: none;" onkeyup="filterTable()">
                </div>

                <a id="excelExportBtn" href="{{ route('applications.approved.export', array_merge(request()->all(), ['category' => $categorySlug])) }}" class="btn-custom" style="height: 36px; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; padding: 0 1rem; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.825rem; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2); white-space: nowrap;" title="Download Excel report with current filter data">
                    <i class="bx bxs-file-export" style="font-size: 1.05rem;"></i> Export Excel
                </a>
            </div>

        </div>
    </form>
</div>
