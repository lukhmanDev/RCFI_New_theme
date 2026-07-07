<?php

$dir = __DIR__ . '/../resources/views/applications';

$files = [
    'cultural_center.blade.php' => [
        'is_group' => true,
        // Regex to match Add Modal address block: from location label row to state input row
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="location"[^>]*>Location.*?</label>.*?<label[^>]*for="state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        // Regex to match Edit Modal address block: from edit_location label row to edit_state input row
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_location"[^>]*>Location.*?</label>.*?<label[^>]*for="edit_state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_location\'\)\.value\s*=\s*meta\.location.*?;.*?document\.getElementById\(\'edit_state\'\)\.value\s*=\s*meta\.state\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?Location:.*?<\/tr>.*?District\s*\/\s*State:.*?<\/tr>#s',
    ],
    'education_center.blade.php' => [
        'is_group' => true,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="location"[^>]*>Location.*?</label>.*?<label[^>]*for="state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_location"[^>]*>Location.*?</label>.*?<label[^>]*for="edit_state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_location\'\)\.value\s*=\s*meta\.location.*?;.*?document\.getElementById\(\'edit_state\'\)\.value\s*=\s*meta\.state\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?Location:.*?<\/tr>.*?District\s*\/\s*State:.*?<\/tr>#s',
    ],
    'hospital_clinics.blade.php' => [
        'is_group' => true,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="place"[^>]*>Place.*?</label>.*?<label[^>]*for="state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_place"[^>]*>Place.*?</label>.*?<label[^>]*for="edit_state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_place\'\)\.value\s*=\s*meta\.place.*?;.*?document\.getElementById\(\'edit_state\'\)\.value\s*=\s*meta\.state\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?Place:.*?<\/tr>.*?District\s*\/\s*State:.*?<\/tr>#s',
    ],
    'shops_others.blade.php' => [
        'is_group' => true,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="place"[^>]*>Place.*?</label>.*?<label[^>]*for="state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_place"[^>]*>Place.*?</label>.*?<label[^>]*for="edit_state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_place\'\)\.value\s*=\s*meta\.place.*?;.*?document\.getElementById\(\'edit_state\'\)\.value\s*=\s*meta\.state\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?Place:.*?<\/tr>.*?District\s*\/\s*State:.*?<\/tr>#s',
    ],
    'drinking_water_group.blade.php' => [
        'is_group' => true,
        'add_pattern' => '#<div>\s*<label[^>]*for="location"[^>]*>Location.*?</label>.*?<label[^>]*for="pin"[^>]*>Pin.*?</label>\s*<input[^>]*name="meta\[pin\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div>\s*<label[^>]*for="edit_location"[^>]*>Location.*?</label>.*?<label[^>]*for="edit_pin"[^>]*>Pin.*?</label>\s*<input[^>]*name="meta\[pin\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_location\'\)\.value\s*=\s*meta\.location.*?;.*?document\.getElementById\(\'edit_pin\'\)\.value\s*=\s*meta\.pin\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?Location\s*\/\s*Address:.*?<\/tr>.*?District\s*\/\s*State\s*\/\s*Pin:.*?<\/tr>#s',
    ],
    'drinking_water_individual.blade.php' => [
        'is_group' => false,
        'add_pattern' => '#<div>\s*<label[^>]*for="location"[^>]*>Location.*?</label>.*?<label[^>]*for="pin"[^>]*>Pin.*?</label>\s*<input[^>]*name="meta\[pin\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div>\s*<label[^>]*for="edit_location"[^>]*>Location.*?</label>.*?<label[^>]*for="edit_pin"[^>]*>Pin.*?</label>\s*<input[^>]*name="meta\[pin\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_location\'\)\.value\s*=\s*meta\.location.*?;.*?document\.getElementById\(\'edit_pin\'\)\.value\s*=\s*meta\.pin\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?Location\s*\/\s*Address:.*?<\/tr>.*?District\s*\/\s*State\s*\/\s*Pin:.*?<\/tr>#s',
    ],
    'house.blade.php' => [
        'is_group' => false,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="edit_state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_house_name\'\)\.value\s*=\s*meta\.house_name.*?;.*?document\.getElementById\(\'edit_state\'\)\.value\s*=\s*meta\.state\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?House Name\s*\/\s*Place:.*?<\/tr>.*?Pin Code:.*?<\/tr>#s',
    ],
    'general.blade.php' => [
        'is_group' => false,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="edit_state"[^>]*>State.*?</label>\s*<input[^>]*name="meta\[state\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_house_name\'\)\.value\s*=\s*meta\.house_name.*?;.*?document\.getElementById\(\'edit_state\'\)\.value\s*=\s*meta\.state\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?House\s*\/\s*Ward:.*?<\/tr>.*?District\s*\/\s*State\s*\/\s*Pin:.*?<\/tr>#s',
    ],
    'family_aid.blade.php' => [
        'is_group' => false,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="pin_code"[^>]*>Pin Code.*?</label>\s*<input[^>]*name="meta\[pin_code\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="edit_pin_code"[^>]*>Pin Code.*?</label>\s*<input[^>]*name="meta\[pin_code\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_house_name\'\)\.value\s*=\s*meta\.house_name.*?;.*?document\.getElementById\(\'edit_pin_code\'\)\.value\s*=\s*meta\.pin_code\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?House\s*\/\s*Place:.*?<\/tr>.*?Pin Code\s*\/ \s*Contact:.*?<\/tr>#s',
    ],
    'differently_abled.blade.php' => [
        'is_group' => false,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="pin_code"[^>]*>Pin Code.*?</label>\s*<input[^>]*name="meta\[pin_code\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="edit_pin_code"[^>]*>Pin Code.*?</label>\s*<input[^>]*name="meta\[pin_code\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_house_name\'\)\.value\s*=\s*meta\.house_name.*?;.*?document\.getElementById\(\'edit_pin_code\'\)\.value\s*=\s*meta\.pin_code\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?House\s*\/\s*Place:.*?<\/tr>.*?Pin Code\s*\/ \s*Contact:.*?<\/tr>#s',
    ],
    'orphan_care.blade.php' => [
        'is_group' => false,
        'add_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="pin_code"[^>]*>Pin Code.*?</label>\s*<input[^>]*name="meta\[pin_code\]"[^>]*>\s*</div>\s*</div>#s',
        'edit_pattern' => '#<div style="display:\s*grid;[^>]*>\s*<div>\s*<label[^>]*for="edit_house_name"[^>]*>House Name.*?</label>.*?<label[^>]*for="edit_pin_code"[^>]*>Pin Code.*?</label>\s*<input[^>]*name="meta\[pin_code\]"[^>]*>\s*</div>\s*</div>#s',
        'js_pattern' => '#document\.getElementById\(\'edit_house_name\'\)\.value\s*=\s*meta\.house_name.*?;.*?document\.getElementById\(\'edit_pin_code\'\)\.value\s*=\s*meta\.pin_code\s*\|\|\s*\'\';#s',
        'details_pattern' => '#<tr[^>]*>.*?House\s*\/\s*Town:.*?<\/tr>.*?District\s*\/\s*State\s*\/\s*Pin:.*?<\/tr>#s',
    ]
];

foreach ($files as $filename => $config) {
    $path = "$dir/$filename";
    if (!file_exists($path)) {
        echo "File not found: $filename\n";
        continue;
    }
    
    $content = file_get_contents($path);
    
    // Normalize line endings
    $content = str_replace("\r\n", "\n", $content);
    
    // 1. Replace Add block
    $addRepl = "                    @include('applications.address_form_fields', ['idPrefix' => '', 'app' => null])";
    if (preg_match($config['add_pattern'], $content)) {
        $content = preg_replace($config['add_pattern'], $addRepl, $content, 1);
        echo "Successfully matched & replaced ADD address block in $filename\n";
    } else {
        echo "ADD pattern NOT matched in $filename\n";
    }

    // 2. Replace Edit block
    $editRepl = "                    @include('applications.address_form_fields', ['idPrefix' => 'edit_', 'app' => null])";
    if (preg_match($config['edit_pattern'], $content)) {
        $content = preg_replace($config['edit_pattern'], $editRepl, $content, 1);
        echo "Successfully matched & replaced EDIT address block in $filename\n";
    } else {
        echo "EDIT pattern NOT matched in $filename\n";
    }

    // 3. Replace JS Populate block
    $jsRepl = "            if (document.getElementById('edit_house_name')) { document.getElementById('edit_house_name').value = appItem.house_name || ''; }
            if (document.getElementById('edit_place')) { document.getElementById('edit_place').value = appItem.place || ''; }
            if (document.getElementById('edit_post_office')) { document.getElementById('edit_post_office').value = appItem.post_office || ''; }
            if (document.getElementById('edit_village')) { document.getElementById('edit_village').value = appItem.village || ''; }
            if (document.getElementById('edit_panchayat')) { document.getElementById('edit_panchayat').value = appItem.panchayat || ''; }
            if (document.getElementById('edit_district')) { document.getElementById('edit_district').value = appItem.district || ''; }
            if (document.getElementById('edit_state')) { document.getElementById('edit_state').value = appItem.state || ''; }
            if (document.getElementById('edit_pin_code')) { document.getElementById('edit_pin_code').value = appItem.pin_code || ''; }";
            
    if (preg_match($config['js_pattern'], $content)) {
        $content = preg_replace($config['js_pattern'], $jsRepl, $content, 1);
        echo "Successfully matched & replaced JS Populate block in $filename\n";
    } else {
        echo "JS Populate pattern NOT matched in $filename\n";
    }

    // 4. Replace Details block
    $detailsRepl = "                            <tr style=\"border-bottom: 1px solid rgba(255,255,255,0.02);\"><td style=\"padding: 0.5rem 0; font-weight: 600; width: 140px;\">Address Details:</td><td>
                                " . ($config['is_group'] ? '' : '${appItem.house_name ? \'House: \' + formatVal(appItem.house_name) + \'<br>\' : \'\'}') . "
                                Place: \${formatVal(appItem.place)}<br>
                                Post Office: \${formatVal(appItem.post_office)}<br>
                                Village: \${formatVal(appItem.village)}<br>
                                Panchayath: \${formatVal(appItem.panchayat)}<br>
                                District / State: \${formatVal(appItem.district)} / \${formatVal(appItem.state)}<br>
                                Pin Code: \${formatVal(appItem.pin_code)}
                            </td></tr>";
                            
    if (preg_match($config['details_pattern'], $content)) {
        $content = preg_replace($config['details_pattern'], $detailsRepl, $content, 1);
        echo "Successfully matched & replaced Details block in $filename\n";
    } else {
        echo "Details pattern NOT matched in $filename\n";
    }
    
    // Save modifications
    file_put_contents($path, $content);
}

echo "Standardization complete!\n";
