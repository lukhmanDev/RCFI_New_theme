import os, re

project_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi"

search_dirs = [
    os.path.join(project_dir, "resources", "views"),
    os.path.join(project_dir, "app"),
]

replacements = [
    ("Donors & Partners", "Agencies"),
    ("Partners & Donors", "Agencies"),
    ("Donors / Partners", "Agencies"),
    ("Partners / Donors", "Agencies"),
    ("Donors/Partners", "Agencies"),
    ("Partners/Donors", "Agencies"),
    ("Donor & Partner", "Agency"),
    ("Partner & Donor", "Agency"),
    ("Donor / Partner", "Agency"),
    ("Partner / Donor", "Agency"),
    ("Donor/Partner", "Agency"),
    ("Partner/Donor", "Agency"),
    ("donor / partner", "agency"),
    ("partner / donor", "agency"),
    ("partner/donor", "agency"),
    ("donor/partner", "agency"),

    ("Registered Donors / Partners", "Registered Agencies"),
    ("Registered Partners & Donors", "Registered Agencies"),
    ("Registered Partners", "Registered Agencies"),
    ("Registered Donors", "Registered Agencies"),
    ("registered partners/donors", "registered agencies"),
    ("registered partners", "registered agencies"),
    ("registered donors", "registered agencies"),

    ("Add Partner", "Add Agency"),
    ("Add Donor", "Add Agency"),
    ("Edit Partner Details", "Edit Agency Details"),
    ("Edit Donor Details", "Edit Agency Details"),
    ("Edit Partner", "Edit Agency"),
    ("Edit Donor", "Edit Agency"),

    ("Partner Details", "Agency Details"),
    ("Donor Details", "Agency Details"),
    ("Type of Partner", "Type of Agency"),
    ("Type of Donor", "Type of Agency"),
    ("Finacial Partner", "Financial Agency"),
    ("Financial Partner", "Financial Agency"),
    ("Non-Financial Partner", "Non-Financial Agency"),
    ("Finacial-Partner", "Financial-Agency"),
    ("Financial-Partner", "Financial-Agency"),
    ("Non-Financial-Partner", "Non-Financial-Agency"),

    ("Partner Name", "Agency Name"),
    ("Donor Name", "Agency Name"),
    ("Short Name of Partner", "Short Name of Agency"),
    ("Short Name of Donor", "Short Name of Agency"),
    ("Partner Website", "Agency Website"),
    ("Donor Website", "Agency Website"),
    ("Partner Logo", "Agency Logo"),
    ("Donor Logo", "Agency Logo"),

    ("Select a donor", "Select an agency"),
    ("Select a Donor", "Select an Agency"),
    ("Select Donor", "Select Agency"),
    ("Select Partner", "Select Agency"),

    ("<span>Donors</span>", "<span>Agencies</span>"),
    ("<span>Partners</span>", "<span>Agencies</span>"),
    ("Donors &amp; Partners", "Agencies"),
    ("Partners &amp; Donors", "Agencies"),
]

updated_files = []

for sdir in search_dirs:
    for root, _, files in os.walk(sdir):
        for file in files:
            if file.endswith('.php') or file.endswith('.blade.php'):
                filepath = os.path.join(root, file)
                with open(filepath, 'r', encoding='utf-8') as f:
                    content = f.read()

                orig_content = content
                for old_text, new_text in replacements:
                    content = content.replace(old_text, new_text)

                if content != orig_content:
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(content)
                    relpath = os.path.relpath(filepath, project_dir)
                    updated_files.append(relpath)

print(f"Updated {len(updated_files)} files:")
for uf in updated_files:
    print(f" - {uf}")
