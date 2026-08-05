import os, re

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views"

matches = []
for root, dirs, files in os.walk(views_dir):
    for f in files:
        if f.endswith('.blade.php'):
            filepath = os.path.join(root, f)
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as file:
                lines = file.readlines()
                for idx, line in enumerate(lines, 1):
                    if '/' in line and any(k in line for k in ['District', 'State', 'Pin', 'Post', 'Panchayath', 'Panchayat', 'Contact', 'Locality', 'Village', 'Place']):
                        if '<td' in line or '<tr' in line or 'formatVal' in line:
                            rel_path = os.path.relpath(filepath, views_dir)
                            matches.append((rel_path, idx, line.strip()))

for rel_path, idx, line in sorted(matches):
    print(f"{rel_path}:{idx}: {line}")
