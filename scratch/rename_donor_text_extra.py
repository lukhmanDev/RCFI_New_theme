import os

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views"

updated_count = 0

for root, _, files in os.walk(views_dir):
    for file in files:
        if file.endswith('.blade.php'):
            fpath = os.path.join(root, file)
            with open(fpath, 'r', encoding='utf-8') as f:
                content = f.read()

            orig_content = content
            content = content.replace("Agency / Donor", "Agency")
            content = content.replace("Donor / Agency", "Agency")
            content = content.replace("Donor:", "Agency:")
            content = content.replace("Donor :", "Agency:")
            content = content.replace("\\nDonor:", "\\nAgency:")

            if content != orig_content:
                with open(fpath, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_count += 1
                print(f"Updated {file}")

print(f"Updated {updated_count} files in extra pass.")
