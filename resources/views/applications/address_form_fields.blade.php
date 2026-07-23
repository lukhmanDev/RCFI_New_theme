@php
    $prefix = $idPrefix ?? '';
@endphp

<!-- Address & Contact Details -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}house_name">House Name *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}house_name" name="meta[house_name]" placeholder="Enter house name" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}place">Place *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}place" name="meta[place]" placeholder="Enter place" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}village">Village *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}village" name="meta[village]" placeholder="Enter village" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}post_office">P.O. *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}post_office" name="meta[post_office]" placeholder="Enter post office" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}panchayat">Panchayath *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}panchayat" name="meta[panchayat]" placeholder="Enter panchayath" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}district">District *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}district" name="meta[district]" placeholder="Enter district" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}state">State *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}state" name="meta[state]" placeholder="Enter state" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}pin_code">Pin Code *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}pin_code" name="meta[pin_code]" placeholder="Enter pin code" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}mobile_1">Mobile 1 *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}mobile_1" name="meta[mobile_1]" placeholder="Enter mobile 1" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}mobile_2">Mobile 2</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}mobile_2" name="meta[mobile_2]" placeholder="Enter mobile 2 (optional)">
    </div>
</div>

