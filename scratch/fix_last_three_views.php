<?php

$dir = __DIR__ . '/../resources/views/applications';

$replacements = [
    'general.blade.php' => [
        'searches' => [
            // ADD
            '                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="address">Current Mailing Address *</label>
                        <textarea class="form-control-dark" id="address" name="meta[address]" style="height: 50px;" required>{{ old(\'meta.address\') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="ward">Ward *</label>
                            <input type="text" class="form-control-dark" id="ward" name="meta[ward]" value="{{ old(\'meta.ward\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">POST *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old(\'meta.post\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="village">Village *</label>
                            <input type="text" class="form-control-dark" id="village" name="meta[village]" value="{{ old(\'meta.village\') }}" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="panchayat_municipality_corporation">Panchayat/Municipality/Corporation *</label>
                        <input type="text" class="form-control-dark" id="panchayat_municipality_corporation" name="meta[panchayat_municipality_corporation]" value="{{ old(\'meta.panchayat_municipality_corporation\') }}" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="block">Block *</label>
                            <input type="text" class="form-control-dark" id="block" name="meta[block]" value="{{ old(\'meta.block\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old(\'meta.district\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old(\'meta.state\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin" name="meta[pin]" value="{{ old(\'meta.pin\') }}" required>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="block">Block *</label>
                            <input type="text" class="form-control-dark" id="block" name="meta[block]" value="{{ old(\'meta.block\') }}" required>
                        </div>
                    </div>',
            // EDIT
            '                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_address">Current Mailing Address *</label>
                        <textarea class="form-control-dark" id="edit_address" name="meta[address]" style="height: 50px;" required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_ward">Ward *</label>
                            <input type="text" class="form-control-dark" id="edit_ward" name="meta[ward]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">POST *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_village" name="meta[village]" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_panchayat_municipality_corporation">Panchayat/Municipality/Corporation *</label>
                        <input type="text" class="form-control-dark" id="edit_panchayat_municipality_corporation" name="meta[panchayat_municipality_corporation]" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_block">Block *</label>
                            <input type="text" class="form-control-dark" id="edit_block" name="meta[block]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_state" name="meta[state]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_pin">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin" name="meta[pin]" required>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'edit_\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_block">Block *</label>
                            <input type="text" class="form-control-dark" id="edit_block" name="meta[block]" required>
                        </div>
                    </div>',
            // JS Populate
            '            document.getElementById(\'edit_address\').value = meta.address || \'\';
            document.getElementById(\'edit_ward\').value = meta.ward || \'\';
            document.getElementById(\'edit_post\').value = meta.post || \'\';
            document.getElementById(\'edit_village\').value = meta.village || \'\';
            document.getElementById(\'edit_panchayat_municipality_corporation\').value = meta.panchayat_municipality_corporation || \'\';
            document.getElementById(\'edit_block\').value = meta.block || \'\';
            document.getElementById(\'edit_district\').value = meta.district || \'\';
            document.getElementById(\'edit_state\').value = meta.state || \'\';
            document.getElementById(\'edit_pin\').value = meta.pin || \'\';' => '            if (document.getElementById(\'edit_house_name\')) { document.getElementById(\'edit_house_name\').value = appItem.house_name || \'\'; }
            if (document.getElementById(\'edit_place\')) { document.getElementById(\'edit_place\').value = appItem.place || \'\'; }
            if (document.getElementById(\'edit_post_office\')) { document.getElementById(\'edit_post_office\').value = appItem.post_office || \'\'; }
            if (document.getElementById(\'edit_village\')) { document.getElementById(\'edit_village\').value = appItem.village || \'\'; }
            if (document.getElementById(\'edit_panchayat\')) { document.getElementById(\'edit_panchayat\').value = appItem.panchayat || \'\'; }
            if (document.getElementById(\'edit_district\')) { document.getElementById(\'edit_district\').value = appItem.district || \'\'; }
            if (document.getElementById(\'edit_state\')) { document.getElementById(\'edit_state\').value = appItem.state || \'\'; }
            if (document.getElementById(\'edit_pin_code\')) { document.getElementById(\'edit_pin_code\').value = appItem.pin_code || \'\'; }
            document.getElementById(\'edit_block\').value = meta.block || \'\';'
        ]
    ],
    'family_aid.blade.php' => [
        'searches' => [
            // ADD
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
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
            // EDIT
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
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
                    <div style="display: grid; grid-template-columns: 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">'
        ]
    ],
    'differently_abled.blade.php' => [
        'searches' => [
            // ADD
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old(\'meta.house_name\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="place">Place *</label>
                            <input type="text" class="form-control-dark" id="place" name="meta[place]" value="{{ old(\'meta.place\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="panchayat">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="panchayat" name="meta[panchayat]" value="{{ old(\'meta.panchayat\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old(\'meta.district\') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="pincode">Pincode *</label>
                            <input type="text" class="form-control-dark" id="pincode" name="meta[pincode]" value="{{ old(\'meta.pincode\') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="mobile">Mobile *</label>
                            <input type="text" class="form-control-dark" id="mobile" name="meta[mobile]" value="{{ old(\'meta.mobile\') }}" required>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mobile">Mobile *</label>
                            <input type="text" class="form-control-dark" id="mobile" name="meta[mobile]" value="{{ old(\'meta.mobile\') }}" required>
                        </div>
                    </div>',
            // EDIT
            '                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_place">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_place" name="meta[place]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_panchayat">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayat" name="meta[panchayat]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_pincode">Pincode *</label>
                            <input type="text" class="form-control-dark" id="edit_pincode" name="meta[pincode]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_mobile">Mobile *</label>
                            <input type="text" class="form-control-dark" id="edit_mobile" name="meta[mobile]" required>
                        </div>
                    </div>' => '                    @include(\'applications.address_form_fields\', [\'idPrefix\' => \'edit_\', \'app\' => null])
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mobile">Mobile *</label>
                            <input type="text" class="form-control-dark" id="edit_mobile" name="meta[mobile]" required>
                        </div>
                    </div>',
            // JS Populate
            '            document.getElementById(\'edit_house_name\').value = meta.house_name || \'\';
            document.getElementById(\'edit_place\').value = meta.place || \'\';
            document.getElementById(\'edit_panchayat\').value = meta.panchayat || \'\';
            document.getElementById(\'edit_district\').value = meta.district || \'\';
            document.getElementById(\'edit_pincode\').value = meta.pincode || \'\';
            document.getElementById(\'edit_mobile\').value = meta.mobile || \'\';' => '            if (document.getElementById(\'edit_house_name\')) { document.getElementById(\'edit_house_name\').value = appItem.house_name || \'\'; }
            if (document.getElementById(\'edit_place\')) { document.getElementById(\'edit_place\').value = appItem.place || \'\'; }
            if (document.getElementById(\'edit_post_office\')) { document.getElementById(\'edit_post_office\').value = appItem.post_office || \'\'; }
            if (document.getElementById(\'edit_village\')) { document.getElementById(\'edit_village\').value = appItem.village || \'\'; }
            if (document.getElementById(\'edit_panchayat\')) { document.getElementById(\'edit_panchayat\').value = appItem.panchayat || \'\'; }
            if (document.getElementById(\'edit_district\')) { document.getElementById(\'edit_district\').value = appItem.district || \'\'; }
            if (document.getElementById(\'edit_state\')) { document.getElementById(\'edit_state\').value = appItem.state || \'\'; }
            if (document.getElementById(\'edit_pin_code\')) { document.getElementById(\'edit_pin_code\').value = appItem.pin_code || \'\'; }
            document.getElementById(\'edit_mobile\').value = meta.mobile || \'\';'
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
        file_put_contents($path, $content);
    }
}

echo "Standardization of remaining views complete!\n";
