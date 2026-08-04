import os, glob

project_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi"

search_dirs = [
    os.path.join(project_dir, "app"),
    os.path.join(project_dir, "resources", "views")
]

replaced_files = []

for sdir in search_dirs:
    for root, _, files in os.walk(sdir):
        for file in files:
            if file.endswith(".php") or file.endswith(".blade.php"):
                filepath = os.path.join(root, file)
                with open(filepath, "r", encoding="utf-8") as f:
                    content = f.read()

                if "locality_location" in content:
                    new_content = content.replace("locality_location", "locality_place")
                    with open(filepath, "w", encoding="utf-8") as f:
                        f.write(new_content)
                    replaced_files.append(os.path.relpath(filepath, project_dir))

print("Replaced locality_location with locality_place in:")
for rf in replaced_files:
    print(f" - {rf}")
