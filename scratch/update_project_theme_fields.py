import os
import re

projects_dir = r"d:\LUKMAN\RCFI\New folder\rcfi\resources\views\projects"
project_detail_dir = r"d:\LUKMAN\RCFI\New folder\rcfi\resources\views\admin\project_detail"

# Form blocks to inject
add_theme_html = """
                <div class="form-group-custom">
                    <label for="add_theme">Theme</label>
                    <select name="theme" id="add_theme" required onchange="populateSubthemes('add_theme', 'add_subtheme')">
                        <option value="">Select Theme</option>
                        <option value="Education">Education</option>
                        <option value="Health & Sanitation">Health & Sanitation</option>
                        <option value="Livelihood & Community Development">Livelihood & Community Development</option>
                        <option value="Housing & Shelter">Housing & Shelter</option>
                        <option value="Social Welfare & Aid">Social Welfare & Aid</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="add_subtheme">Subtheme</label>
                    <select name="subtheme" id="add_subtheme" required>
                        <option value="">Select Subtheme</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="add_activity">Activity</label>
                    <input type="text" name="activity" id="add_activity" required placeholder="Enter activity">
                </div>"""

edit_theme_html = """
                <div class="form-group-custom">
                    <label for="edit_theme">Theme</label>
                    <select name="theme" id="edit_theme" required onchange="populateSubthemes('edit_theme', 'edit_subtheme')">
                        <option value="">Select Theme</option>
                        <option value="Education">Education</option>
                        <option value="Health & Sanitation">Health & Sanitation</option>
                        <option value="Livelihood & Community Development">Livelihood & Community Development</option>
                        <option value="Housing & Shelter">Housing & Shelter</option>
                        <option value="Social Welfare & Aid">Social Welfare & Aid</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_subtheme">Subtheme</label>
                    <select name="subtheme" id="edit_subtheme" required>
                        <option value="">Select Subtheme</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_activity">Activity</label>
                    <input type="text" name="activity" id="edit_activity" required placeholder="Enter activity">
                </div>"""

js_inject_code = """
        const currentProj = (typeof project !== 'undefined' ? project : (typeof projectData !== 'undefined' ? projectData : {}));
        document.getElementById('edit_theme').value = currentProj.theme || '';
        populateSubthemes('edit_theme', 'edit_subtheme', currentProj.subtheme || '');
        document.getElementById('edit_activity').value = currentProj.activity || '';"""

js_helpers_code = """
    const themesData = {
        "Education": [
            "Education Center Infrastructure",
            "Scholarships & Sponsorships",
            "School Kits & Learning Materials",
            "Vocational & Skill Training"
        ],
        "Health & Sanitation": [
            "Hospital/Clinic Infrastructure",
            "Drinking Water Systems",
            "Sanitation & Hygiene Facilities",
            "Medical Camps & Aid"
        ],
        "Livelihood & Community Development": [
            "Shops & Small Businesses Support",
            "Cultural & Community Centers",
            "Agricultural & Allied Activities",
            "Women Empowerment Initiatives"
        ],
        "Housing & Shelter": [
            "House Construction",
            "Shelter Reconstruction & Renovation",
            "Disaster Relief Housing"
        ],
        "Social Welfare & Aid": [
            "Orphan Care & Support",
            "Differently Abled Assistance",
            "Family Aid & Pension Schemes",
            "General Charities & Relief Aid"
        ]
    };

    function populateSubthemes(themeId, subthemeId, selectedSubtheme = '') {
        const themeSelect = document.getElementById(themeId);
        const subthemeSelect = document.getElementById(subthemeId);
        if (!themeSelect || !subthemeSelect) return;

        const selectedTheme = themeSelect.value;
        subthemeSelect.innerHTML = '<option value="">Select Subtheme</option>';

        if (selectedTheme && themesData[selectedTheme]) {
            themesData[selectedTheme].forEach(sub => {
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

def update_project_files():
    for filename in os.listdir(projects_dir):
        if not filename.endswith('.blade.php'):
            continue
        filepath = os.path.join(projects_dir, filename)
        print(f"Processing project list view: {filename}")
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # 1. Inject Theme, Subtheme, Activity in Add Project Modal
        # We look for label for="available_budget" div block in the add section
        # The add section ID or fields have name="available_budget" and id="available_budget"
        add_budget_pattern = re.compile(
            r'(<div class="form-group-custom">\s*<label for="available_budget">Available Budget</label>[\s\S]*?<\/div>)',
            re.IGNORECASE
        )
        if "id=\"add_theme\"" not in content:
            content = add_budget_pattern.sub(r'\1' + add_theme_html, content, count=1)

        # 2. Inject Theme, Subtheme, Activity in Edit Project Modal
        # The edit section fields have id="edit_available_budget"
        edit_budget_pattern = re.compile(
            r'(<div class="form-group-custom">\s*<label for="edit_available_budget">Available Budget</label>[\s\S]*?<\/div>)',
            re.IGNORECASE
        )
        if "id=\"edit_theme\"" not in content:
            content = edit_budget_pattern.sub(r'\1' + edit_theme_html, content, count=1)

        # 3. Inject JS values set in openEditModal
        # We look for edit_remarks set value:
        remarks_pattern = re.compile(
            r"(document\.getElementById\(['\"]edit_remarks['\"]\)\.value\s*=\s*(?:project\.remarks|projectData\.remarks)\s*\|\|\s*['\"]['\"];?)",
            re.IGNORECASE
        )
        if "currentProj" not in content:
            content = remarks_pattern.sub(r'\1' + js_inject_code, content, count=1)

        # 4. Inject JS themesData and populateSubthemes helper functions right before </script>
        if "populateSubthemes" not in content:
            content = content.replace("</script>", js_helpers_code + "\n</script>")

        # 5. Inject details alert parameters
        alert_pattern = re.compile(
            r"(alert\('Project Details:\\nID: \{\{\s*\$project->project_id\s*\}\}\\nName: \{\{\s*\$project->project_name \?\? 'N/A'\s*\}\}\\nSponsor: \{\{\s*\$project->sponsor \?\? 'N/A'\s*\}\})(.*?)",
            re.IGNORECASE
        )
        if "Theme:" not in content:
            replacement = r"\1\\nTheme: {{ $project->theme ?? 'N/A' }}\\nSubtheme: {{ $project->subtheme ?? 'N/A' }}\\nActivity: {{ $project->activity ?? 'N/A' }}\2"
            content = alert_pattern.sub(replacement, content)

        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)

def update_detail_views():
    for filename in os.listdir(project_detail_dir):
        if not filename.endswith('.blade.php'):
            continue
        filepath = os.path.join(project_detail_dir, filename)
        print(f"Processing project detail view: {filename}")
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # Inject Theme, Subtheme, Activity in details-grid
        # We search for: <div class="details-label">Type of Project</div><div class="details-colon">:</div><div class="details-value">{{ $project->type_of_project }}</div>
        type_pattern = re.compile(
            r'(<div class="details-label">Type of Project</div><div class="details-colon">:</div><div class="details-value">\{\{\s*\$project->type_of_project\s*\}\}</div>)',
            re.IGNORECASE
        )
        detail_fields = """
                    <div class="details-label">Theme</div><div class="details-colon">:</div><div class="details-value">{{ $project->theme ?? 'N/A' }}</div>
                    <div class="details-label">Subtheme</div><div class="details-colon">:</div><div class="details-value">{{ $project->subtheme ?? 'N/A' }}</div>
                    <div class="details-label">Activity</div><div class="details-colon">:</div><div class="details-value">{{ $project->activity ?? 'N/A' }}</div>"""

        if "details-label\">Theme" not in content:
            content = type_pattern.sub(r'\1' + detail_fields, content, count=1)

        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)

if __name__ == "__main__":
    update_project_files()
    update_detail_views()
    print("Done!")
