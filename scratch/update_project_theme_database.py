import os
import re

projects_dir = r"d:\LUKMAN\RCFI\New folder\rcfi\resources\views\projects"

add_theme_html = """
                <div class="form-group-custom">
                    <label for="add_theme">Theme</label>
                    <select name="theme" id="add_theme" required onchange="populateSubthemes('add_theme', 'add_subtheme')">
                        <option value="">Select Theme</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->name }}" data-theme-id="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="add_subtheme">Subtheme</label>
                    <select name="subtheme" id="add_subtheme" required>
                        <option value="">Select Subtheme</option>
                    </select>
                </div>"""

edit_theme_html = """
                <div class="form-group-custom">
                    <label for="edit_theme">Theme</label>
                    <select name="theme" id="edit_theme" required onchange="populateSubthemes('edit_theme', 'edit_subtheme')">
                        <option value="">Select Theme</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->name }}" data-theme-id="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_subtheme">Subtheme</label>
                    <select name="subtheme" id="edit_subtheme" required>
                        <option value="">Select Subtheme</option>
                    </select>
                </div>"""

js_helpers_code = """
    const themesData = {
        @foreach($themes as $t)
            "{{ $t->id }}": [
                @foreach($subthemes->where('theme_id', $t->id) as $st)
                    {!! json_encode($st->name) !!},
                @endforeach
            ],
        @endforeach
    };

    function populateSubthemes(themeId, subthemeId, selectedSubtheme = '') {
        const themeSelect = document.getElementById(themeId);
        const subthemeSelect = document.getElementById(subthemeId);
        if (!themeSelect || !subthemeSelect) return;

        const selectedOption = themeSelect.options[themeSelect.selectedIndex];
        const themeIdVal = selectedOption ? selectedOption.getAttribute('data-theme-id') : null;
        subthemeSelect.innerHTML = '<option value="">Select Subtheme</option>';

        if (themeIdVal && themesData[themeIdVal]) {
            themesData[themeIdVal].forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.textContent = sub;
                if (sub === selectedSubtheme) {
                    option.selected = true;
                }
                subthemeSelect.appendChild(option);
            });
        }
    }
"""

def main():
    for filename in os.listdir(projects_dir):
        if not filename.endswith('.blade.php'):
            continue
        filepath = os.path.join(projects_dir, filename)
        print(f"Updating database themes fetch in: {filename}")
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # Replace Add Theme Select
        add_pattern = re.compile(
            r'<div class="form-group-custom">\s*<label for="add_theme">Theme</label>[\s\S]*?<select name="subtheme" id="add_subtheme" required>[\s\S]*?</select>\s*</div>',
            re.IGNORECASE
        )
        content = add_pattern.sub(add_theme_html.strip(), content)

        # Replace Edit Theme Select
        edit_pattern = re.compile(
            r'<div class="form-group-custom">\s*<label for="edit_theme">Theme</label>[\s\S]*?<select name="subtheme" id="edit_subtheme" required>[\s\S]*?</select>\s*</div>',
            re.IGNORECASE
        )
        content = edit_pattern.sub(edit_theme_html.strip(), content)

        # Replace js_helpers populateSubthemes and themesData
        # Remove the previous static themesData block up to populateSubthemes function closing bracket
        js_func_pattern = re.compile(
            r'const themesData = \{[\s\S]*?\}\s*\}\s*\}',
            re.IGNORECASE
        )
        if js_func_pattern.search(content):
            content = js_func_pattern.sub('', content)

        # Remove duplicate populateSubthemes definitions if any
        content = content.replace("function populateSubthemes(themeId, subthemeId, selectedSubtheme = '') {", "")

        # Always inject the dynamic js_helpers_code before </script>
        # Clean up first to avoid duplicating
        content = content.replace(js_helpers_code, "")
        content = content.replace("</script>", js_helpers_code + "\n</script>")

        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)

if __name__ == '__main__':
    main()
