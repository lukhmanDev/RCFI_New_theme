@extends('layouts.admin')

@section('title', 'Donors & Partners')

@section('content')

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class=\"alert alert-success\" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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

    <!-- Donors List Panel -->
    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title">Partners & Donors</h2>
            @if(Auth::user()->role === 1)
            <button onclick="openModal()" class="btn-custom">
                <i class="bx bx-plus-circle"></i> Add Partner
            </button>
            @endif
        </div>
        
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 80px;">Logo</th>
                        <th>Partner Details</th>
                        <th>Type of Partner</th>
                        <th>Type of Fund</th>
                        <th>Contact Details</th>
                        <th style="text-align: center;">Initiated At</th>
                        <th style="text-align: center;">Website</th>
                        @if(Auth::user()->role === 1)
                        <th style="text-align: center;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($donors as $donor)
                        <tr>
                            <!-- Image / Logo Thumbnail -->
                            <td style="text-align: center; vertical-align: middle;">
                                @if($donor->image_path)
                                    <img src="{{ asset('storage/' . $donor->image_path) }}" alt="Logo" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid var(--panel-border);">
                                @else
                                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #1f2937; display: flex; align-items: center; justify-content: center; border: 1px solid var(--panel-border);">
                                        <i class="bx bxs-business" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                    </div>
                                @endif
                            </td>

                            <!-- Partner Details -->
                            <td>
                                <div style="font-weight: 600; color: #ffffff;">{{ $donor->name }}</div>
                                @if($donor->short_name)
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">({{ $donor->short_name }})</div>
                                @endif
                            </td>

                            <!-- Type of Partner -->
                            <td>{{ str_replace('-', ' ', $donor->type_of_partner) }}</td>

                            <!-- Type of Fund -->
                            <td>
                                <span style="background-color: rgba(6, 182, 212, 0.15); color: var(--accent-cyan); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                    {{ str_replace('-', ' ', $donor->type_of_fund) }}
                                </span>
                            </td>

                            <!-- Contact details -->
                            <td>
                                @if($donor->contact_person)
                                    <div style="font-weight: 500; color: var(--text-main); font-size: 0.85rem;">{{ $donor->contact_person }}</div>
                                @endif
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $donor->email ?? 'No email' }} | {{ $donor->phone ?? 'No phone' }}
                                </div>
                            </td>

                            <!-- Support Initiated -->
                            <td style="text-align: center; font-size: 0.85rem;">
                                {{ $donor->support_initiated_at ? date('M, Y', strtotime($donor->support_initiated_at)) : 'N/A' }}
                            </td>

                            <!-- Website -->
                            <td style="text-align: center;">
                                @if($donor->website)
                                    <a href="{{ $donor->website }}" target="_blank" style="color: var(--accent-cyan); font-size: 1.25rem;"><i class="bx bx-globe"></i></a>
                                @else
                                    <span style="color: var(--panel-border);">-</span>
                                @endif
                            </td>

                            <!-- Action buttons -->
                            @if(Auth::user()->role === 1)
                            <td style="text-align: center; white-space: nowrap;">
                                <button onclick="openEditModal({{ json_encode($donor) }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Edit"><i class="bx bx-pencil"></i></button>
                                
                                <button type="button" class="btn-danger-custom" onclick="openDeleteModal({{ $donor->id }}, '{{ addslashes($donor->name) }}')" style="padding: 0.4rem; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Delete"><i class="bx bx-trash"></i></button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 1 ? 8 : 7 }}" style="text-align: center; padding: 2rem;">No registered partners/donors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Partner Modal Dialog -->
    <div id="addPartnerModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 500px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Partner</h2>
            </div>

            <form action="{{ route('donors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Partner Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="name">Partner Name</label>
                    <input type="text" class="form-control-dark" id="name" name="name" placeholder="Enter partner name" value="{{ old('name') }}" required>
                </div>

                <!-- Short Name of Partner -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="short_name">Short Name of Partner</label>
                    <input type="text" class="form-control-dark" id="short_name" name="short_name" placeholder="Enter short name" value="{{ old('short_name') }}">
                </div>

                <!-- Partner Website -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="website">Partner Website</label>
                    <input type="url" class="form-control-dark" id="website" name="website" placeholder="https://example.com" value="{{ old('website') }}">
                </div>

                <!-- Split Row: Type of Partner & Fund -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label class="form-label" for="type_of_partner">Type of Partner</label>
                        <select class="form-select-dark" id="type_of_partner" name="type_of_partner" required>
                            <option value="">Select</option>
                            <option value="Finacial-Partner" {{ old('type_of_partner') == 'Finacial-Partner' ? 'selected' : '' }}>Finacial Partner </option>
                            <option value="Non-Financial-Partner" {{ old('type_of_partner') == 'Non-Financial-Partner' ? 'selected' : '' }}>Non-Financial Partner</option>
                        </select>
                    </div>  
                    <div style="flex: 1;">
                        <label class="form-label" for="type_of_fund">Type of Fund</label>
                        <select class="form-select-dark" id="type_of_fund" name="type_of_fund" required>
                            <option value="">Select</option>
                            <option value="Foreign-Currency" {{ old('type_of_fund') == 'Foreign-Currency' ? 'selected' : '' }}>Foreign Currency</option>
                            <option value="Local-Currency" {{ old('type_of_fund') == 'Local-Currency' ? 'selected' : '' }}>Local Currency</option>
                            <option value="Not-Applicable" {{ old('type_of_fund') == 'Not-Applicable' ? 'selected' : '' }}>Not Applicable</option>
                          
                        </select>
                    </div>
                </div>

                <!-- Contact Person -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="contact_person">Contact Person</label>
                    <input type="text" class="form-control-dark" id="contact_person" name="contact_person" placeholder="Enter contact person name" value="{{ old('contact_person') }}">
                </div>

                <!-- Month, Year which support was initiated -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="support_initiated_at">Month, Year which support was initiated</label>
                    <input type="month" class="form-control-dark" id="support_initiated_at" name="support_initiated_at" value="{{ old('support_initiated_at') }}">
                </div>

                <!-- Contact Email -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="email">Contact Email</label>
                    <input type="email" class="form-control-dark" id="email" name="email" placeholder="Enter contact email address" value="{{ old('email') }}">
                </div>

                <!-- Contact Phone -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="phone">Contact Phone</label>
                    <input type="text" class="form-control-dark" id="phone" name="phone" placeholder="Enter contact phone number" value="{{ old('phone') }}">
                </div>

                <!-- Image Details / Logo Upload -->
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="image">Partner Logo / Image</label>
                    <input type="file" class="form-control-dark" id="image" name="image" accept="image/*">
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">Supported formats: JPEG, PNG, JPG. Max 2MB.</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Add Partner
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Partner Modal Dialog -->
    <div id="editPartnerModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 500px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Partner Details</h2>
            </div>

            <form id="editPartnerForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Partner Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_name">Partner Name</label>
                    <input type="text" class="form-control-dark" id="edit_name" name="name" required>
                </div>

                <!-- Short Name of Partner -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_short_name">Short Name of Partner</label>
                    <input type="text" class="form-control-dark" id="edit_short_name" name="short_name">
                </div>

                <!-- Partner Website -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_website">Partner Website</label>
                    <input type="url" class="form-control-dark" id="edit_website" name="website">
                </div>

                <!-- Split Row: Type of Partner & Fund -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <label class="form-label" for="edit_type_of_partner">Type of Partner</label>
                        <select class="form-select-dark" id="edit_type_of_partner" name="type_of_partner" required>
                            <option value="Finacial-Partner">Finacial Partner</option>
                            <option value="Non-Financial-Partner">Non-Financial Partner</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label" for="edit_type_of_fund">Type of Fund</label>
                        <select class="form-select-dark" id="edit_type_of_fund" name="type_of_fund" required>
                            <option value="Foreign-Currency">Foreign Currency</option>
                            <option value="Local-Currency">Local Currency</option>
                            <option value="Not-Applicable">Not Applicable</option>
                        </select>
                    </div>
                </div>

                <!-- Contact Person -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_contact_person">Contact Person</label>
                    <input type="text" class="form-control-dark" id="edit_contact_person" name="contact_person">
                </div>

                <!-- Month, Year which support was initiated -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_support_initiated_at">Month, Year which support was initiated</label>
                    <input type="month" class="form-control-dark" id="edit_support_initiated_at" name="support_initiated_at">
                </div>

                <!-- Contact Email -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_email">Contact Email</label>
                    <input type="email" class="form-control-dark" id="edit_email" name="email">
                </div>

                <!-- Contact Phone -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_phone">Contact Phone</label>
                    <input type="text" class="form-control-dark" id="edit_phone" name="phone">
                </div>

                <!-- Current Image Preview -->
                <div id="edit_image_preview_container" style="margin-bottom: 1rem; display: none;">
                    <label class="form-label">Current Logo</label>
                    <img id="edit_image_preview" src="" alt="Current Logo" style="width: 64px; height: 64px; border-radius: 8px; object-fit: cover;">
                </div>

                <!-- Image Details / Logo Upload -->
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="edit_image">Replace Partner Logo / Image</label>
                    <input type="file" class="form-control-dark" id="edit_image" name="image" accept="image/*">
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">Leave blank to keep current logo.</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        // Add Partner Modal Toggle
        function openModal() {
            document.getElementById('addPartnerModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addPartnerModal').style.display = 'none';
        }

        // Edit Partner Modal Toggle
        function openEditModal(donor) {
            const form = document.getElementById('editPartnerForm');
            form.action = '/admin/donors/' + donor.id;

            document.getElementById('edit_name').value = donor.name;
            document.getElementById('edit_short_name').value = donor.short_name || '';
            document.getElementById('edit_website').value = donor.website || '';
            document.getElementById('edit_type_of_partner').value = donor.type_of_partner;
            document.getElementById('edit_type_of_fund').value = donor.type_of_fund;
            document.getElementById('edit_contact_person').value = donor.contact_person || '';
            document.getElementById('edit_support_initiated_at').value = donor.support_initiated_at || '';
            document.getElementById('edit_email').value = donor.email || '';
            document.getElementById('edit_phone').value = donor.phone || '';

            const previewContainer = document.getElementById('edit_image_preview_container');
            const previewImage = document.getElementById('edit_image_preview');
            if (donor.image_path) {
                previewImage.src = '/storage/' + donor.image_path;
                previewContainer.style.display = 'block';
            } else {
                previewContainer.style.display = 'none';
            }

            document.getElementById('editPartnerModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editPartnerModal').style.display = 'none';
        }

        function openDeleteModal(id, name) {
            const form = document.getElementById('deleteConfirmationForm');
            form.action = '/admin/donors/' + id;
            document.getElementById('deleteConfirmationText').textContent = 'Are you sure you want to delete ' + name + '?';
            document.getElementById('deleteConfirmationModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmationModal').style.display = 'none';
        }
    </script>

    @include('admin.delete_confirmation')

    <!-- Automatically open add modal if validation error occurs on new partner creation -->
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                openModal();
            });
        </script>
    @endif

@endsection
