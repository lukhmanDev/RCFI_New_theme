import os, re

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\applications"

for root, _, files in os.walk(views_dir):
    for f in files:
        if f.endswith('.blade.php'):
            fpath = os.path.join(root, f)
            with open(fpath, 'r', encoding='utf-8') as file:
                content = file.read()
            rec_inputs = re.findall(r'name=["\'](?:meta\[)?(recommender_[a-zA-Z0-9_]+|recommendation_[a-zA-Z0-9_]+)\]?["\']', content)
            if rec_inputs:
                print(f"{f}: {set(rec_inputs)}")
