import os, re

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views"

# 1. Update JS view modals in applications/ and approved_applications/
app_views_dirs = [
    os.path.join(views_dir, "applications"),
    os.path.join(views_dir, "approved_applications")
]

for sdir in app_views_dirs:
    if not os.path.exists(sdir):
        continue
    for f in os.listdir(sdir):
        if not f.endswith('.blade.php'):
            continue
        fpath = os.path.join(sdir, f)
        with open(fpath, 'r', encoding='utf-8') as file:
            content = file.read()

        # Check if function openViewModal exists
        if "function openViewModal(" in content:
            # Replace old recommendation block or add if missing
            old_rec_block_pat = r'\$\{?\s*\(?\s*meta\.recommendation_name[\s\S]*?</div>\s*`?\s*\:?\s*[\'"][\'"]\s*\}?'
            
            new_rec_js = """
                const recName = meta.recommender_name || meta.recommendation_name || appItem.recommender_name || '';
                const recOrg = meta.recommender_org || meta.recommendation_organization || appItem.recommender_org || '';
                const recOrgOther = meta.recommender_org_other || meta.recommendation_organization_other || '';
                const recPhone = meta.recommender_phone || meta.recommendation_phone || appItem.recommender_phone || '';
                const recPos = meta.recommender_position || meta.recommendation_position || appItem.recommender_position || '';
                const displayOrg = (recOrg === 'Others') ? (recOrgOther || 'Others') : recOrg;

                const recHtml = (recName || recOrg || recPhone || recPos) ? `
                <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                    <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; font-weight: 700;">Recommendation Details</h5>
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        ${recName ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; width: 140px;">Recommender Name:</td><td>${formatVal(recName)}</td></tr>` : ''}
                        ${recOrg ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600;">Organization:</td><td>${formatVal(displayOrg)}</td></tr>` : ''}
                        ${recPhone ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600;">Phone:</td><td>${formatVal(recPhone)}</td></tr>` : ''}
                        ${recPos ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600;">Position / Designation:</td><td>${formatVal(recPos)}</td></tr>` : ''}
                    </table>
                </div>` : '';
"""
            # Check if recHtml is already defined
            if "const recHtml =" not in content:
                content = content.replace("function openViewModal(appItem) {", "function openViewModal(appItem) {\n" + new_rec_js)
            
            # Now replace where recommendation section is rendered or insert before closing modal html
            if "Recommendation Details" in content:
                content = re.sub(
                    r'\$\{\s*\(\s*meta\.recommendation_name[\s\S]*?\}\s*`\s*:\s*[\'"][\'"]\s*\}',
                    '${recHtml}',
                    content
                )
            elif "${recHtml}" not in content:
                content = content.replace(
                    "No additional notes provided.\n                    </p>\n                </div>",
                    "No additional notes provided.\n                    </p>\n                </div>\n                ${recHtml}"
                )

            with open(fpath, 'w', encoding='utf-8') as file:
                file.write(content)
            print(f"Updated JS View Modal in {f}")


# 2. Update Admin project detail blade templates
admin_detail_dir = os.path.join(views_dir, "admin", "project_detail")
admin_detail_files = [os.path.join(admin_detail_dir, f) for f in os.listdir(admin_detail_dir) if f.endswith('.blade.php')]
admin_detail_files.append(os.path.join(views_dir, "admin", "social_aid_project_detals.blade.php"))

blade_rec_snippet = """
                        @php
                            $recName = $metaData['recommender_name'] ?? ($metaData['recommendation_name'] ?? null);
                            $recOrg = $metaData['recommender_org'] ?? ($metaData['recommendation_organization'] ?? null);
                            $recOrgOther = $metaData['recommender_org_other'] ?? ($metaData['recommendation_organization_other'] ?? null);
                            $recPhone = $metaData['recommender_phone'] ?? ($metaData['recommendation_phone'] ?? null);
                            $recPos = $metaData['recommender_position'] ?? ($metaData['recommendation_position'] ?? null);
                            $displayOrg = ($recOrg === 'Others') ? ($recOrgOther ?: 'Others') : $recOrg;
                        @endphp

                        @if($recName || $recOrg || $recPhone || $recPos)
                        <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                            <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; font-weight: 700;">Recommendation Details</h5>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                @if($recName)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Recommender Name:</td><td>{!! $formatVal($recName) !!}</td></tr>@endif
                                @if($recOrg)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-muted);">Organization:</td><td>{!! $formatVal($displayOrg) !!}</td></tr>@endif
                                @if($recPhone)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-muted);">Phone:</td><td>{!! $formatVal($recPhone) !!}</td></tr>@endif
                                @if($recPos)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-muted);">Position / Designation:</td><td>{!! $formatVal($recPos) !!}</td></tr>@endif
                            </table>
                        </div>
                        @endif
"""

for fpath in admin_detail_files:
    if not os.path.exists(fpath):
        continue
    with open(fpath, 'r', encoding='utf-8') as file:
        content = file.read()

    if "Recommendation Details" not in content and "Additional Notes:" in content:
        content = content.replace(
            "{{ $application->details ? $application->details : 'No additional notes provided.' }}\n                            </p>\n                        </div>",
            "{{ $application->details ? $application->details : 'No additional notes provided.' }}\n                            </p>\n                        </div>" + blade_rec_snippet
        )
        with open(fpath, 'w', encoding='utf-8') as file:
            file.write(content)
        print(f"Updated Admin Project Detail in {os.path.basename(fpath)}")

print("Completed adding Recommendation Details to view modals and project detail views.")
