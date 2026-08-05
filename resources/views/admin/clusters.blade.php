@extends('layouts.admin')

@section('title', 'Clusters')

@section('content')

<style>
    #clustersTableBody tr[data-search]:hover td {
        background-color: #f0fdf4 !important;
        color: #0f172a !important;
    }
    #clustersTable thead th {
        font-size: 0.78rem;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        padding: 0.85rem 1rem;
    }
    #clustersTable tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
    }
    #clusterSearch:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
    }
    #clusterSearch::placeholder {
        color: #9ca3af;
    }
</style>


    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff9999; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            <ul style="list-style-position: inside; margin: 0; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Clusters List Panel -->
    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <h2 class="panel-title">Clusters</h2>
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <!-- Search Box -->
                <div style="position: relative; display: flex; align-items: center;">
                    <i class="bx bx-search" style="position: absolute; left: 0.6rem; color: #64748b; font-size: 1rem; pointer-events: none;"></i>
                    <input type="text" id="clusterSearch" onkeyup="filterClusters()" placeholder="Search clusters..." style="padding: 0.45rem 0.75rem 0.45rem 2rem; background: #ffffff; border: 1.5px solid #d1d5db; color: #1e293b; border-radius: 6px; font-size: 0.85rem; outline: none; min-width: 210px; transition: border-color 0.2s;">
                </div>
                @if($canManage)
                <!-- Excel Download -->
                <a href="{{ route('clusters.export') }}" class="btn-custom" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; font-size: 0.85rem; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.4); color: #10b981; text-decoration: none; border-radius: 6px;">
                    <i class="bx bx-download"></i> Export CSV
                </a>
                <!-- Add Cluster -->
                <button onclick="openModal()" class="btn-custom">
                    <i class="bx bx-plus-circle"></i> Add Cluster
                </button>
                @endif
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" id="clustersTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Cluster Name</th>
                        <th>Institution Name</th>
                        <th>Address / Location</th>
                        <th>Contact info</th>
                        <th>Remarks</th>
                        @if($canManage)
                        <th style="text-align: center; width: 80px;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="clustersTableBody">
                    @forelse($clusters as $cluster)
                        @php
                            $searchText = strtolower(implode(' ', array_filter([
                                $cluster->code, $cluster->name, $cluster->institution_name,
                                $cluster->head_of_institution, $cluster->place, $cluster->po,
                                $cluster->village, $cluster->panjayath, $cluster->dist, $cluster->state,
                                $cluster->contact_no, $cluster->cordinator_name, $cluster->remarks,
                            ])));
                        @endphp
                        <tr data-search="{{ $searchText }}">
                            <!-- Code -->
                            <td style="font-weight: 700; color: #059669; font-size: 0.88rem; letter-spacing: 0.03em;">{{ $cluster->code ?: '—' }}</td>

                            <!-- Cluster Name -->
                            <td>
                                <div style="font-weight: 700; color: #1e293b; font-size: 0.92rem;">{{ $cluster->name }}</div>
                            </td>

                            <!-- Institution Name -->
                            <td style="color: #334155; font-size: 0.88rem;">{{ $cluster->institution_name ?: '—' }}</td>

                            <!-- Address/Location -->
                            <td style="max-width: 240px; white-space: normal; word-wrap: break-word; font-size: 0.82rem; color: #475569; line-height: 1.5;">
                                @php
                                    $locParts = array_filter([
                                        $cluster->place,
                                        $cluster->po ? 'P.O ' . $cluster->po : null,
                                        $cluster->village,
                                        $cluster->panjayath ? 'Panchayath: ' . $cluster->panjayath : null,
                                        $cluster->dist ? $cluster->dist . ' Dist' : null,
                                        $cluster->state
                                    ]);
                                @endphp
                                {{ implode(', ', $locParts) ?: '—' }}
                            </td>

                            <!-- Contact info -->
                            <td style="font-size: 0.82rem; line-height: 1.7; color: #1e293b;">
                                @if($cluster->contact_no)
                                    <div><span style="color: #64748b; font-weight: 600;">Ph:</span> {{ $cluster->contact_no }}</div>
                                @endif
                                @if($cluster->head_of_institution)
                                    <div><span style="color: #64748b; font-weight: 600;">Head:</span> {{ $cluster->head_of_institution }}</div>
                                @endif
                                @if($cluster->head_contact_number)
                                    <div><span style="color: #64748b; font-weight: 600;">Head Ph:</span> {{ $cluster->head_contact_number }}</div>
                                @endif
                                @if($cluster->cordinator_name)
                                    <div><span style="color: #64748b; font-weight: 600;">Coord:</span> {{ $cluster->cordinator_name }}</div>
                                @endif
                                @if($cluster->cordinator_contact_number)
                                    <div><span style="color: #64748b; font-weight: 600;">Coord Ph:</span> {{ $cluster->cordinator_contact_number }}</div>
                                @endif
                                @if(!$cluster->contact_no && !$cluster->head_of_institution && !$cluster->head_contact_number && !$cluster->cordinator_name && !$cluster->cordinator_contact_number)
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>

                            <!-- Remarks -->
                            <td style="max-width: 180px; white-space: normal; word-wrap: break-word; color: #475569; font-size: 0.83rem;">{{ $cluster->remarks ?: '—' }}</td>

                            <!-- Action buttons -->
                            @if($isSuperAdmin)
                            <td style="text-align: center; white-space: nowrap;">
                                <button onclick="openEditModal({{ json_encode($cluster) }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Edit">
                                    <i class="bx bx-pencil"></i>
                                </button>

                                <form action="{{ route('clusters.destroy', $cluster->id) }}" method="POST" style="display: inline-flex; margin-left: 0.3rem;" onsubmit="return confirm('Are you sure you want to delete this cluster?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @elseif($canManage)
                            <td style="text-align: center;">—</td>
                            @endif
                        </tr>
                    @empty
                        <tr id="no-results-row" style="display: none;">
                            <td colspan="{{ $canManage ? 7 : 6 }}" style="text-align: center; padding: 2rem; color: var(--text-muted);">No clusters match your search.</td>
                        </tr>
                        <tr>
                            <td colspan="{{ $canManage ? 7 : 6 }}" style="text-align: center; padding: 2rem; color: var(--text-muted);">No registered clusters found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Cluster Modal Dialog -->
    <div id="addClusterModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 600px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Cluster</h2>
            </div>

            <form action="{{ route('clusters.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Cluster Code -->
                    <div>
                        <label class="form-label" for="code">Cluster Code</label>
                        <input type="text" class="form-control-dark" id="code" name="code" placeholder="e.g. CLT01" value="{{ old('code') }}">
                    </div>

                    <!-- Cluster Name -->
                    <div>
                        <label class="form-label" for="name">Cluster Name</label>
                        <input type="text" class="form-control-dark" id="name" name="name" placeholder="Enter name" value="{{ old('name') }}" required>
                    </div>
                </div>

                <!-- Institution Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="institution_name">Institution Name</label>
                    <input type="text" class="form-control-dark" id="institution_name" name="institution_name" placeholder="Enter institution name" value="{{ old('institution_name') }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Head of Institute -->
                    <div>
                        <label class="form-label" for="head_of_institution">Head of Institute</label>
                        <input type="text" class="form-control-dark" id="head_of_institution" name="head_of_institution" placeholder="Enter Head of Institute" value="{{ old('head_of_institution') }}">
                    </div>

                    <!-- Contact No of Head -->
                    <div>
                        <label class="form-label" for="head_contact_number">Contact No of Head</label>
                        <input type="text" class="form-control-dark" id="head_contact_number" name="head_contact_number" placeholder="Enter head contact no" value="{{ old('head_contact_number') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Place -->
                    <div>
                        <label class="form-label" for="place">Place</label>
                        <input type="text" class="form-control-dark" id="place" name="place" placeholder="Enter place" value="{{ old('place') }}">
                    </div>

                    <!-- P/O -->
                    <div>
                        <label class="form-label" for="po">Post Office (P/O)</label>
                        <input type="text" class="form-control-dark" id="po" name="po" placeholder="Enter P/O" value="{{ old('po') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Village -->
                    <div>
                        <label class="form-label" for="village">Village</label>
                        <input type="text" class="form-control-dark" id="village" name="village" placeholder="Enter village" value="{{ old('village') }}">
                    </div>

                    <!-- Panjayath -->
                    <div>
                        <label class="form-label" for="panjayath">Panjayath</label>
                        <input type="text" class="form-control-dark" id="panjayath" name="panjayath" placeholder="Enter panjayath" value="{{ old('panjayath') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Dist -->
                    <div>
                        <label class="form-label" for="dist">District (Dist)</label>
                        <input type="text" class="form-control-dark" id="dist" name="dist" placeholder="Enter district" value="{{ old('dist') }}">
                    </div>

                    <!-- State -->
                    <div>
                        <label class="form-label" for="state">State</label>
                        <input type="text" class="form-control-dark" id="state" name="state" placeholder="Enter state" value="{{ old('state', 'Kerala') }}">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Contact No -->
                    <div>
                        <label class="form-label" for="contact_no">Contact No</label>
                        <input type="text" class="form-control-dark" id="contact_no" name="contact_no" placeholder="Enter contact no" value="{{ old('contact_no') }}">
                    </div>

                    <!-- Coordinator Name -->
                    <div>
                        <label class="form-label" for="cordinator_name">Coordinator Name</label>
                        <input type="text" class="form-control-dark" id="cordinator_name" name="cordinator_name" placeholder="Enter coordinator name" value="{{ old('cordinator_name') }}">
                    </div>
                </div>

                <!-- Coordinator Contact Number -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="cordinator_contact_number">Coordinator Contact Number</label>
                    <input type="text" class="form-control-dark" id="cordinator_contact_number" name="cordinator_contact_number" placeholder="Enter coordinator contact no" value="{{ old('cordinator_contact_number') }}">
                </div>

                <!-- Remarks -->
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea class="form-control-dark" id="remarks" name="remarks" placeholder="Enter remarks" style="min-height: 80px;">{{ old('remarks') }}</textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Add Cluster
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Cluster Modal Dialog -->
    <div id="editClusterModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 600px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Cluster</h2>
            </div>

            <form id="editClusterForm" method="POST">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Cluster Code -->
                    <div>
                        <label class="form-label" for="edit_code">Cluster Code</label>
                        <input type="text" class="form-control-dark" id="edit_code" name="code" required>
                    </div>

                    <!-- Cluster Name -->
                    <div>
                        <label class="form-label" for="edit_name">Cluster Name</label>
                        <input type="text" class="form-control-dark" id="edit_name" name="name" required>
                    </div>
                </div>

                <!-- Institution Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_institution_name">Institution Name</label>
                    <input type="text" class="form-control-dark" id="edit_institution_name" name="institution_name">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Head of Institute -->
                    <div>
                        <label class="form-label" for="edit_head_of_institution">Head of Institute</label>
                        <input type="text" class="form-control-dark" id="edit_head_of_institution" name="head_of_institution">
                    </div>

                    <!-- Contact No of Head -->
                    <div>
                        <label class="form-label" for="edit_head_contact_number">Contact No of Head</label>
                        <input type="text" class="form-control-dark" id="edit_head_contact_number" name="head_contact_number">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Place -->
                    <div>
                        <label class="form-label" for="edit_place">Place</label>
                        <input type="text" class="form-control-dark" id="edit_place" name="place">
                    </div>

                    <!-- P/O -->
                    <div>
                        <label class="form-label" for="edit_po">Post Office (P/O)</label>
                        <input type="text" class="form-control-dark" id="edit_po" name="po">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Village -->
                    <div>
                        <label class="form-label" for="edit_village">Village</label>
                        <input type="text" class="form-control-dark" id="edit_village" name="village">
                    </div>

                    <!-- Panjayath -->
                    <div>
                        <label class="form-label" for="edit_panjayath">Panjayath</label>
                        <input type="text" class="form-control-dark" id="edit_panjayath" name="panjayath">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Dist -->
                    <div>
                        <label class="form-label" for="edit_dist">District (Dist)</label>
                        <input type="text" class="form-control-dark" id="edit_dist" name="dist">
                    </div>

                    <!-- State -->
                    <div>
                        <label class="form-label" for="edit_state">State</label>
                        <input type="text" class="form-control-dark" id="edit_state" name="state">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Contact No -->
                    <div>
                        <label class="form-label" for="edit_contact_no">Contact No</label>
                        <input type="text" class="form-control-dark" id="edit_contact_no" name="contact_no">
                    </div>

                    <!-- Coordinator Name -->
                    <div>
                        <label class="form-label" for="edit_cordinator_name">Coordinator Name</label>
                        <input type="text" class="form-control-dark" id="edit_cordinator_name" name="cordinator_name">
                    </div>
                </div>

                <!-- Coordinator Contact Number -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_cordinator_contact_number">Coordinator Contact Number</label>
                    <input type="text" class="form-control-dark" id="edit_cordinator_contact_number" name="cordinator_contact_number">
                </div>

                <!-- Remarks -->
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="edit_remarks">Remarks</label>
                    <textarea class="form-control-dark" id="edit_remarks" name="remarks" style="min-height: 80px;"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Update Cluster
                </button>
            </form>
        </div>
    </div>

    <!-- Script Block -->
    <script>
        function openModal() {
        const modal = document.getElementById('addClusterModal') || document.getElementById("addAppModal") || document.getElementById("addProjectModal") || document.getElementById("addModal");
        if (modal) modal.style.display = "flex";
    }

        function closeModal() {
            const modal = document.getElementById('addClusterModal') || document.getElementById('addAppModal') || document.getElementById('addModal');
            if (modal) modal.style.display = 'none';
        }

        function openEditModal(clusterItem) {
            const form = document.getElementById('editClusterForm');
            form.action = '/admin/clusters/' + clusterItem.id;

            document.getElementById('edit_code').value = clusterItem.code || '';
            document.getElementById('edit_name').value = clusterItem.name || '';
            document.getElementById('edit_institution_name').value = clusterItem.institution_name || '';
            document.getElementById('edit_head_of_institution').value = clusterItem.head_of_institution || '';
            document.getElementById('edit_head_contact_number').value = clusterItem.head_contact_number || '';
            document.getElementById('edit_place').value = clusterItem.place || '';
            document.getElementById('edit_po').value = clusterItem.po || '';
            document.getElementById('edit_village').value = clusterItem.village || '';
            document.getElementById('edit_panjayath').value = clusterItem.panjayath || '';
            document.getElementById('edit_dist').value = clusterItem.dist || '';
            document.getElementById('edit_state').value = clusterItem.state || '';
            document.getElementById('edit_contact_no').value = clusterItem.contact_no || '';
            document.getElementById('edit_cordinator_name').value = clusterItem.cordinator_name || '';
            document.getElementById('edit_cordinator_contact_number').value = clusterItem.cordinator_contact_number || '';
            document.getElementById('edit_remarks').value = clusterItem.remarks || '';

            document.getElementById('editClusterModal').style.display = 'flex';
        }

        function closeEditModal() {
            const modal = document.getElementById('editClusterModal') || document.getElementById('editAppModal') || document.getElementById('editModal');
            if (modal) modal.style.display = 'none';
        }

        function filterClusters() {
            const query = document.getElementById('clusterSearch').value.toLowerCase().trim();
            const tbody = document.getElementById('clustersTableBody');
            const rows = tbody.querySelectorAll('tr[data-search]');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.getAttribute('data-search') || '';
                if (text.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no-results row
            const noResultsRow = document.getElementById('no-results-row');
            if (noResultsRow) {
                noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }
    
        // Global Window Bindings
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.openEditModal = openEditModal;
        window.closeEditModal = closeEditModal;
    </script>

@endsection
