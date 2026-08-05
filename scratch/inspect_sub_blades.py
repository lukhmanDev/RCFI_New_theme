import os, re

sub_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\admin\project_detail"

for f in os.listdir(sub_dir):
    if f.endswith('.blade.php'):
        filepath = os.path.join(sub_dir, f)
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as file:
            content = file.read()
            print(f"=== {f} ===")
            for line in content.splitlines():
                if any(k in line for k in ['District / State', 'Contact 1 / 2', 'Village / Post', 'Gender / Age', 'Panchayat / P.O.']):
                    print("  ", line.strip())
