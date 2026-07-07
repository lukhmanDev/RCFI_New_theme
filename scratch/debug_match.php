<?php

$dir = __DIR__ . '/../resources/views/applications';
$file = 'drinking_water_group.blade.php';
$path = "$dir/$file";
$content = file_get_contents($path);
$content = str_replace("\r\n", "\n", $content);

$search = '                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Location / Address:</td><td>${formatVal(meta.location)} / ${formatVal(meta.address)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village / PO / Panch:</td><td>${formatVal(meta.village)} / ${formatVal(meta.post)} / ${formatVal(meta.panchayath)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Dist / State / Pin:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)} / ${formatVal(meta.pin)}</td></tr>';

$search = str_replace("\r\n", "\n", $search);

echo "Strpos check: " . (strpos($content, $search) !== false ? "FOUND" : "NOT FOUND") . "\n";

// Let's print a small chunk of the file around Location / Address
if (preg_match('/Location \/ Address.*/', $content, $matches)) {
    echo "Found matching substring in file:\n";
    var_dump($matches[0]);
}

// Let's see if we can find the exact lines
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'Location / Address') !== false) {
        echo "Line " . ($i + 1) . ": " . bin2hex($line) . "\n";
        echo "Search line: " . bin2hex('                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Location / Address:</td><td>${formatVal(meta.location)} / ${formatVal(meta.address)}</td></tr>') . "\n";
    }
}
