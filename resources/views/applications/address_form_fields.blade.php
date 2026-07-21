@php
    $groupCategories = [
        'Education Center',
        'Cultural Center',
        'Hospital or Clinics',
        'Shops and Others',
        'Drinking Water - Group Level'
    ];
    $isGroup = in_array($categoryName, $groupCategories);
@endphp

<!-- Address Details -->
<div style="margin-top: 1.5rem; margin-bottom: 0.75rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
    <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.05em;">Address Details</span>
</div>

@if(!$isGroup)
    <!-- 1. House Name -->
    <div style="margin-bottom: 1rem;">
        <label class="form-label" for="{{ $idPrefix }}house_name">House Name</label>
        <input type="text" class="form-control-dark" id="{{ $idPrefix }}house_name" name="house_name" placeholder="Enter house name" value="{{ $app ? $app->house_name : '' }}">
    </div>
@endif

<!-- 2. Place -->
<div style="margin-bottom: 1rem;">
    <label class="form-label" for="{{ $idPrefix }}place">Place</label>
    <input type="text" class="form-control-dark" id="{{ $idPrefix }}place" name="place" placeholder="Enter place" value="{{ $app ? $app->place : '' }}">
</div>

<!-- 3. Village -->
<div style="margin-bottom: 1rem;">
    <label class="form-label" for="{{ $idPrefix }}village">Village</label>
    <input type="text" class="form-control-dark" id="{{ $idPrefix }}village" name="village" placeholder="Enter village" value="{{ $app ? $app->village : '' }}">
</div>

<!-- 4. Post Office (P.O.) -->
<div style="margin-bottom: 1rem;">
    <label class="form-label" for="{{ $idPrefix }}post_office">Post Office (P.O.)</label>
    <input type="text" class="form-control-dark" id="{{ $idPrefix }}post_office" name="post_office" placeholder="Enter post office" value="{{ $app ? $app->post_office : '' }}">
</div>

<!-- 5. Panchayath -->
<div style="margin-bottom: 1rem;">
    <label class="form-label" for="{{ $idPrefix }}panchayat">Panchayath</label>
    <input type="text" class="form-control-dark" id="{{ $idPrefix }}panchayat" name="panchayat" placeholder="Enter panchayath" value="{{ $app ? $app->panchayat : '' }}">
</div>

<!-- 6. District -->
<div style="margin-bottom: 1rem;">
    <label class="form-label" for="{{ $idPrefix }}district">District</label>
    <input type="text" class="form-control-dark" id="{{ $idPrefix }}district" name="district" placeholder="Enter district" value="{{ $app ? $app->district : '' }}">
</div>

<!-- 7. State -->
<div style="margin-bottom: 1rem;">
    <label class="form-label" for="{{ $idPrefix }}state">State</label>
    <input type="text" class="form-control-dark" id="{{ $idPrefix }}state" name="state" placeholder="Enter state" value="{{ $app ? $app->state : '' }}">
</div>

<!-- 8. Pin Code -->
<div style="margin-bottom: 1rem;">
    <label class="form-label" for="{{ $idPrefix }}pin_code">Pin Code</label>
    <input type="text" class="form-control-dark" id="{{ $idPrefix }}pin_code" name="pin_code" placeholder="Enter pin code" value="{{ $app ? $app->pin_code : '' }}">
</div>
