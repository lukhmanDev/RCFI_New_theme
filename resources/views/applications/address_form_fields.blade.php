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
        <label class="form-label" for="{{ $prefix }}pin_code">Pin Code *</label>
        <input type="tel" class="form-control-dark" id="{{ $prefix }}pin_code" name="meta[pin_code]" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}place">Place *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}place" name="meta[place]" placeholder="Enter place" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}village">Village *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}village" name="meta[village]" placeholder="Enter village" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}post_office">P.O. *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}post_office" name="meta[post_office]" placeholder="Enter post office" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}panchayat">Panchayath *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}panchayat" name="meta[panchayat]" placeholder="Enter panchayath" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}district">District *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}district" name="meta[district]" placeholder="Enter district" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}state">State *</label>
        <input type="text" class="form-control-dark" id="{{ $prefix }}state" name="meta[state]" placeholder="Enter state" required>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
    <div>
        <label class="form-label" for="{{ $prefix }}mobile_1">Mobile 1 *</label>
        <input type="tel" class="form-control-dark" id="{{ $prefix }}mobile_1" name="meta[mobile_1]" placeholder="Enter 10-digit Mobile 1" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}mobile_2">Mobile 2</label>
        <input type="tel" class="form-control-dark" id="{{ $prefix }}mobile_2" name="meta[mobile_2]" placeholder="Enter 10-digit Mobile 2" maxlength="10" inputmode="numeric" pattern="[0-9]{10}">
    </div>
    <div>
        <label class="form-label" for="{{ $prefix }}whatsapp_number">WhatsApp Number *</label>
        <input type="tel" class="form-control-dark" id="{{ $prefix }}whatsapp_number" name="meta[whatsapp_number]" placeholder="Enter 10-digit WhatsApp Number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
    </div>
</div>
