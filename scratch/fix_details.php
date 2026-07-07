<?php

$dir = __DIR__ . '/../resources/views/applications';

$files = [
    'cultural_center.blade.php' => true,
    'drinking_water_group.blade.php' => true,
    'drinking_water_individual.blade.php' => true,
    'education_center.blade.php' => true,
    'hospital_clinics.blade.php' => true,
    'shops_others.blade.php' => true,
    'house.blade.php' => false,
    'general.blade.php' => false,
    'family_aid.blade.php' => false,
    'differently_abled.blade.php' => false,
    'orphan_care.blade.php' => false
];

foreach ($files as $filename => $isGroup) {
    $path = "$dir/$filename";
    if (!file_exists($path)) {
        continue;
    }
    
    $content = file_get_contents($path);
    $content = str_replace("\r\n", "\n", $content);
    
    // Pattern to match any <tr> containing address label columns in details modal
    $rowPattern = '#<tr[^>]*>\s*<td[^>]*>\s*(?:Location|Village|Post|Panch|Dist|Place|PO|Pin|House|Ward|Town)[^<]*:.*?</td>.*?</tr>#is';
    
    // Let's count how many rows match
    if (preg_match_all($rowPattern, $content, $matches)) {
        echo "Found " . count($matches[0]) . " address detail rows in $filename\n";
        
        // We replace all matching rows with a single placeholder
        // To avoid inserting multiple placeholders, we replace the first match with the placeholder and subsequent ones with empty string
        $replaced = false;
        $content = preg_replace_callback($rowPattern, function($m) use (&$replaced, $isGroup) {
            if (!$replaced) {
                $replaced = true;
                return "<!-- __ADDRESS_PLACEHOLDER__ -->";
            }
            return "";
        }, $content);
        
        // Now replace the placeholder with the new standardized address row
        $detailsRepl = "                            <tr style=\"border-bottom: 1px solid rgba(255,255,255,0.02);\"><td style=\"padding: 0.5rem 0; font-weight: 600; width: 140px;\">Address Details:</td><td>
                                " . ($isGroup ? '' : '${appItem.house_name ? \'House: \' + formatVal(appItem.house_name) + \'<br>\' : \'\'}') . "
                                Place: \${formatVal(appItem.place)}<br>
                                Post Office: \${formatVal(appItem.post_office)}<br>
                                Village: \${formatVal(appItem.village)}<br>
                                Panchayath: \${formatVal(appItem.panchayat)}<br>
                                District / State: \${formatVal(appItem.district)} / \${formatVal(appItem.state)}<br>
                                Pin Code: \${formatVal(appItem.pin_code)}
                            </td></tr>";
                            
        $content = str_replace("<!-- __ADDRESS_PLACEHOLDER__ -->", $detailsRepl, $content);
        
        file_put_contents($path, $content);
        echo "Updated Details modal in $filename\n";
    } else {
        echo "No address detail rows found in $filename\n";
    }
}
