import os, re

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views"

search_terms = [
    'District / State',
    'Contact 1 / 2',
    'Pin / Place / Village',
    'Post / Panchayath',
    'Panchayat / P.O.',
    'District / State / Pin'
]

matches = []
for root, dirs, files in os.walk(views_dir):
    for f in files:
        if f.endswith('.blade.php'):
            filepath = os.path.join(root, f)
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as file:
                lines = file.readlines()
                for idx, line in enumerate(lines, 1):
                    for term in search_terms:
                        if term.lower() in line.lower():
                            rel_path = os.path.relpath(filepath, views_dir)
                            matches.append((rel_path, idx, term, line.strip()))

for rel_path, idx, term, line in sorted(matches):
    print(f"{rel_path}:{idx} [{term}]: {line}")
