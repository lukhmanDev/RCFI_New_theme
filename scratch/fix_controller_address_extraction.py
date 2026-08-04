import os

filepath = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\app\Http\Controllers\ApplicationController.php"

with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace address building in store() and update()

old_block = """            $addressFields = ['house_name', 'place', 'post_office', 'town', 'village', 'panchayat', 'district', 'state', 'pin_code', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];
            
            $rawAddress = [];
            foreach ($addressFields as $af) {
                if ($request->filled($af)) {
                    $rawAddress[$af] = $request->input($af);
                } elseif ($request->filled("meta.{$af}")) {
                    $rawAddress[$af] = $request->input("meta.{$af}");
                }
            }

            $dbAddressFields = ['house_name', 'place', 'post_office', 'village', 'panchayat', 'district', 'state', 'pin_code', 'contact_number_1', 'contact_number_2'];
            $addressData = array_intersect_key($rawAddress, array_flip($dbAddressFields));
            if (!isset($addressData['contact_number_1'])) {
                $mob = $rawAddress['mobile_1'] ?? ($rawAddress['mobile'] ?? null);
                if ($mob) { $addressData['contact_number_1'] = $mob; }
            }
            if (!isset($addressData['contact_number_2'])) {
                $mob2 = $rawAddress['mobile_2'] ?? null;
                if ($mob2) { $addressData['contact_number_2'] = $mob2; }
            }"""

new_block = """            $placeVal = $request->input('place') ?? ($request->input('location') ?? ($request->input('meta.place') ?? $request->input('meta.location')));
            $postVal = $request->input('post_office') ?? ($request->input('post') ?? ($request->input('meta.post_office') ?? $request->input('meta.post')));
            $panchayatVal = $request->input('panchayat') ?? ($request->input('panchayath') ?? ($request->input('meta.panchayat') ?? $request->input('meta.panchayath')));
            $pinVal = $request->input('pin_code') ?? ($request->input('pin') ?? ($request->input('meta.pin_code') ?? ($request->input('meta.pin') ?? $request->input('meta.locality_pin_code'))));
            $villageVal = $request->input('village') ?? $request->input('meta.village');
            $districtVal = $request->input('district') ?? $request->input('meta.district');
            $stateVal = $request->input('state') ?? $request->input('meta.state');
            $houseVal = $request->input('house_name') ?? $request->input('meta.house_name');
            $c1Val = $request->input('contact_number_1') ?? ($request->input('mobile_1') ?? ($request->input('mobile') ?? ($request->input('meta.contact_number_1') ?? ($request->input('meta.mobile_1') ?? $request->input('meta.mobile')))));
            $c2Val = $request->input('contact_number_2') ?? ($request->input('mobile_2') ?? ($request->input('meta.contact_number_2') ?? $request->input('meta.mobile_2')));

            $addressData = array_filter([
                'house_name' => $houseVal,
                'place' => $placeVal,
                'post_office' => $postVal,
                'village' => $villageVal,
                'panchayat' => $panchayatVal,
                'district' => $districtVal,
                'state' => $stateVal,
                'pin_code' => $pinVal,
                'contact_number_1' => $c1Val,
                'contact_number_2' => $c2Val,
            ]);
            $addressFields = ['house_name', 'place', 'location', 'post_office', 'post', 'town', 'village', 'panchayat', 'panchayath', 'district', 'state', 'pin_code', 'pin', 'contact_number_1', 'contact_number_2', 'mobile', 'mobile_1', 'mobile_2'];"""

content = content.replace(old_block, new_block)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated ApplicationController.php address handling.")
