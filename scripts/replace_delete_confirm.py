from pathlib import Path
import re

base = Path('resources/views')
translations = {
    r"onsubmit=\"return confirm\('Are you sure you want to delete this application\?'\);\"": 'data-confirm="Are you sure you want to delete this application?"',
    r"onsubmit=\"return confirm\('Are you sure you want to delete this project\?'\);\"": 'data-confirm="Are you sure you want to delete this project?"',
    r"onsubmit=\"return confirm\('Are you sure you want to delete this material\?'\);\"": 'data-confirm="Are you sure you want to delete this material?"',
    r"onsubmit=\"return confirm\('Are you sure you want to delete this expense\?'\);\"": 'data-confirm="Are you sure you want to delete this expense?"',
    r"onsubmit=\"return confirm\('Delete this photo\?'\);\"": 'data-confirm="Are you sure you want to delete this photo?"',
    r"onsubmit=\"return confirm\('Are you sure you want to delete this user\?'\);\"": 'data-confirm="Are you sure you want to delete this user?"',
}

for path in base.rglob('*.blade.php'):
    text = path.read_text(encoding='utf-8')
    updated = text
    for pattern, replacement in translations.items():
        updated = re.sub(pattern, replacement, updated)
    if updated != text:
        path.write_text(updated, encoding='utf-8')
        print(f'Updated: {path}')
