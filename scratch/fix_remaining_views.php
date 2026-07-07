<?php

$dir = __DIR__ . '/../resources/views/applications';

$replacements = [
    'drinking_water_group.blade.php' => [
        'searches' => [
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Location / Address:</td><td>${formatVal(meta.location)} / ${formatVal(meta.address)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village / PO / Panch:</td><td>${formatVal(meta.village)} / ${formatVal(meta.post)} / ${formatVal(meta.panchayath)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Dist / State / Pin:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)} / ${formatVal(meta.pin)}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Address Details:</td><td>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>'
        ]
    ],
    'drinking_water_individual.blade.php' => [
        'searches' => [
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Location:</td><td>${formatVal(meta.location)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Address:</td><td>${formatVal(meta.address)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.village)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.panchayath)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State / Pin:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)} / ${formatVal(meta.pin)}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Address Details:</td><td>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>'
        ]
    ],
    'house.blade.php' => [
        'searches' => [
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old(\'meta.house_name\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="place">Location *</label>
                            <input type="text" class="form-control-dark" id="place" name="meta[place]" value="{{ old(\'meta.place\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="panchayath">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="panchayath" name="meta[panchayath]" value="{{ old(\'meta.panchayath\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">P.O. *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old(\'meta.post\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old(\'meta.pin_code\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old(\'meta.district\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old(\'meta.state\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="gender">Applicant Gender *</label>
                            <select class="form-select-dark" id="gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="gender">Applicant Gender *</label>
                            <select class="form-select-dark" id="gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>',
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_place">Location *</label>
                            <input type="text" class="form-control-dark" id="edit_place" name="meta[place]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_panchayath">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayath" name="meta[panchayath]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">P.O. *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_state" name="meta[state]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_gender">Applicant Gender *</label>
                            <select class="form-select-dark" id="edit_gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'edit_\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_gender">Applicant Gender *</label>
                            <select class="form-select-dark" id="edit_gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>',
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">House Name / Place:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayat / P.O.:</td><td>${formatVal(meta.panchayath)} / ${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; District / State:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin_code)}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Address Details:</td><td>
                                House: ${formatVal(appItem.house_name)}<br>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>',
            // alternative details pattern for house.blade
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">House Name / Place:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayat / P.O.:</td><td>${formatVal(meta.panchayath)} / ${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin_code)}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Address Details:</td><td>
                                House: ${formatVal(appItem.house_name)}<br>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>'
        ]
    ],
    'general.blade.php' => [
        'searches' => [
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old(\'meta.house_name\') }}">
                        </div>
                        <div>
                            <label class="form-label" for="ward">Ward/Location *</label>
                            <input type="text" class="form-control-dark" id="ward" name="meta[ward]" value="{{ old(\'meta.ward\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="panchayat_municipality_corporation">Panchayat/Municipality/Corporation *</label>
                            <input type="text" class="form-control-dark" id="panchayat_municipality_corporation" name="meta[panchayat_municipality_corporation]" value="{{ old(\'meta.panchayat_municipality_corporation\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="village">Village *</label>
                            <input type="text" class="form-control-dark" id="village" name="meta[village]" value="{{ old(\'meta.village\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">Post Office *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old(\'meta.post\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old(\'meta.pin_code\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old(\'meta.district\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old(\'meta.state\') }}" required>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'\', \'app\' => null])',
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_ward">Ward/Location *</label>
                            <input type="text" class="form-control-dark" id="edit_ward" name="meta[ward]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_panchayat_municipality_corporation">Panchayat/Municipality/Corporation *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayat_municipality_corporation" name="meta[panchayat_municipality_corporation]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_village" name="meta[village]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">Post Office *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_state" name="meta[state]" required>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'edit_\', \'app\' => null])',
            '            document.getElementById(\'edit_house_name\').value = meta.house_name || \'\';
            document.getElementById(\'edit_ward\').value = meta.ward || \'\';
            document.getElementById(\'edit_panchayat_municipality_corporation\').value = meta.panchayat_municipality_corporation || \'\';
            document.getElementById(\'edit_village\').value = meta.village || \'\';
            document.getElementById(\'edit_post\').value = meta.post || \'\';
            document.getElementById(\'edit_pin_code\').value = meta.pin_code || \'\';
            document.getElementById(\'edit_district\').value = meta.district || \'\';
            document.getElementById(\'edit_state\').value = meta.state || \'\';' => '            if (document.getElementById(\'edit_house_name\')) { document.getElementById(\'edit_house_name\').value = appItem.house_name || \'\'; }
            if (document.getElementById(\'edit_place\')) { document.getElementById(\'edit_place\').value = appItem.place || \'\'; }
            if (document.getElementById(\'edit_post_office\')) { document.getElementById(\'edit_post_office\').value = appItem.post_office || \'\'; }
            if (document.getElementById(\'edit_village\')) { document.getElementById(\'edit_village\').value = appItem.village || \'\'; }
            if (document.getElementById(\'edit_panchayat\')) { document.getElementById(\'edit_panchayat\').value = appItem.panchayat || \'\'; }
            if (document.getElementById(\'edit_district\')) { document.getElementById(\'edit_district\').value = appItem.district || \'\'; }
            if (document.getElementById(\'edit_state\')) { document.getElementById(\'edit_state\').value = appItem.state || \'\'; }
            if (document.getElementById(\'edit_pin_code\')) { document.getElementById(\'edit_pin_code\').value = appItem.pin_code || \'\'; }',
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">House / Ward:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.ward)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayat/Mun/Corp:</td><td>${formatVal(meta.panchayat_municipality_corporation)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Ward / Village / Post:</td><td>${formatVal(meta.ward)} / ${formatVal(meta.village)} / ${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State / Pin:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)} / ${formatVal(meta.pin_code)}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Address Details:</td><td>
                                House: ${formatVal(appItem.house_name)}<br>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>'
        ]
    ],
    'family_aid.blade.php' => [
        'searches' => [
            '                    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old(\'meta.house_name\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="location">Location *</label>
                            <input type="text" class="form-control-dark" id="location" name="meta[location]" value="{{ old(\'meta.location\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="post_office">P.O. *</label>
                            <input type="text" class="form-control-dark" id="post_office" name="meta[post_office]" value="{{ old(\'meta.post_office\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="panchayat">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="panchayat" name="meta[panchayat]" value="{{ old(\'meta.panchayat\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old(\'meta.district\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old(\'meta.pin_code\') }}" required>
                        </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">',
            '                    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_location">Location *</label>
                            <input type="text" class="form-control-dark" id="edit_location" name="meta[location]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_post_office">P.O. *</label>
                            <input type="text" class="form-control-dark" id="edit_post_office" name="meta[post_office]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_panchayat">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayat" name="meta[panchayat]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" required>
                        </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'edit_\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">',
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">House / Place:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.location)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">PO / Panchayat / Dist:</td><td>${formatVal(meta.post_office)} / ${formatVal(meta.panchayat)} / ${formatVal(meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code / Contact:</td><td>Pin: ${formatVal(meta.pin_code)} / Mob: ${formatVal(meta.mobile_1)} ${meta.mobile_2 ? \', \' + meta.mobile_2 : \'\'}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Address Details:</td><td>
                                House: ${formatVal(appItem.house_name)}<br>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>'
        ]
    ],
    'differently_abled.blade.php' => [
        'searches' => [
            '                    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old(\'meta.house_name\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="place">Place *</label>
                            <input type="text" class="form-control-dark" id="place" name="meta[place]" value="{{ old(\'meta.place\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="post_office">P.O. *</label>
                            <input type="text" class="form-control-dark" id="post_office" name="meta[post_office]" value="{{ old(\'meta.post_office\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="panchayat">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="panchayat" name="meta[panchayat]" value="{{ old(\'meta.panchayat\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old(\'meta.district\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old(\'meta.pin_code\') }}" required>
                        </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">',
            '                    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_place">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_place" name="meta[place]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_post_office">P.O. *</label>
                            <input type="text" class="form-control-dark" id="edit_post_office" name="meta[post_office]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_panchayat">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayat" name="meta[panchayat]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" required>
                        </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'edit_\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">',
            '            document.getElementById(\'edit_house_name\').value = meta.house_name || \'\';
            document.getElementById(\'edit_place\').value = meta.place || \'\';
            document.getElementById(\'edit_post_office\').value = meta.post_office || \'\';
            document.getElementById(\'edit_panchayat\').value = meta.panchayat || \'\';
            document.getElementById(\'edit_district\').value = meta.district || \'\';
            document.getElementById(\'edit_pin_code\').value = meta.pin_code || \'\';' => '            if (document.getElementById(\'edit_house_name\')) { document.getElementById(\'edit_house_name\').value = appItem.house_name || \'\'; }
            if (document.getElementById(\'edit_place\')) { document.getElementById(\'edit_place\').value = appItem.place || \'\'; }
            if (document.getElementById(\'edit_post_office\')) { document.getElementById(\'edit_post_office\').value = appItem.post_office || \'\'; }
            if (document.getElementById(\'edit_village\')) { document.getElementById(\'edit_village\').value = appItem.village || \'\'; }
            if (document.getElementById(\'edit_panchayat\')) { document.getElementById(\'edit_panchayat\').value = appItem.panchayat || \'\'; }
            if (document.getElementById(\'edit_district\')) { document.getElementById(\'edit_district\').value = appItem.district || \'\'; }
            if (document.getElementById(\'edit_state\')) { document.getElementById(\'edit_state\').value = appItem.state || \'\'; }
            if (document.getElementById(\'edit_pin_code\')) { document.getElementById(\'edit_pin_code\').value = appItem.pin_code || \'\'; }',
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">House / Place:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">PO / Panchayat / Dist:</td><td>${formatVal(meta.post_office)} / ${formatVal(meta.panchayat)} / ${formatVal(meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code / Contact:</td><td>Pin: ${formatVal(meta.pin_code)} / Mob: ${formatVal(meta.mobile_1)} ${meta.mobile_2 ? \', \' + meta.mobile_2 : \'\'}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Address Details:</td><td>
                                House: ${formatVal(appItem.house_name)}<br>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>'
        ]
    ],
    'orphan_care.blade.php' => [
        'searches' => [
            '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">House / Town:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.town)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">PO / Panchayat / Dist:</td><td>${formatVal(meta.post_office)} / ${formatVal(meta.panchayat)} / ${formatVal(meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State / Pin:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)} / ${formatVal(meta.pin_code)}</td></tr>' => '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Address Details:</td><td>
                                House: ${formatVal(appItem.house_name)}<br>
                                Place: ${formatVal(appItem.place)}<br>
                                Post Office: ${formatVal(appItem.post_office)}<br>
                                Village: ${formatVal(appItem.village)}<br>
                                Panchayath: ${formatVal(appItem.panchayat)}<br>
                                District / State: ${formatVal(appItem.district)} / ${formatVal(appItem.state)}<br>
                                Pin Code: ${formatVal(appItem.pin_code)}
                            </td></tr>'
        ]
    ]
];

foreach ($replacements as $filename => $config) {
    $path = "$dir/$filename";
    if (!file_exists($path)) {
        echo "File not found: $filename\n";
        continue;
    }
    
    $content = file_get_contents($path);
    
    // Normalize content line endings to LF
    $content = str_replace("\r\n", "\n", $content);
    
    $any = false;
    foreach ($config['searches'] as $search => $replace) {
        // Normalize search string line endings to LF
        $searchNorm = str_replace("\r\n", "\n", $search);
        
        if (strpos($content, $searchNorm) !== false) {
            $content = str_replace($searchNorm, $replace, $content);
            echo "Replaced pattern in $filename\n";
            $any = true;
        } else {
            echo "Pattern not matched in $filename:\n" . substr($searchNorm, 0, 120) . "...\n";
        }
    }
    
    if ($any) {
        // Save back with normalized LF (or let PHP handle it)
        file_put_contents($path, $content);
    }
}

echo "Standardization of remaining views complete!\n";
