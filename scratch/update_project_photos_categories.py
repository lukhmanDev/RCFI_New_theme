import os

directory = r"d:\LUKMAN\RCFI\New folder\rcfi\resources\views\admin\project_detail"

# Target variables initialization block (normalized to \n)
target_variables = """                    $pFiles = $project->files ?? [];
                    $beforePhotos = $pFiles['photos_before'] ?? [];
                    $inbetweenPhotos = $pFiles['photos_inbetween'] ?? [];
                    $afterPhotos = $pFiles['photos_after'] ?? ($pFiles['photos'] ?? []);
                    $inaugurationPhotos = $pFiles['photos_inauguration'] ?? [];
                    $compDetails = $pFiles['completion_details'] ?? [];"""

new_variables = """                    $pFiles = $project->files ?? [];
                    $beforePhotos = $pFiles['photos_before'] ?? [];
                    $startingPhotos = $pFiles['photos_starting'] ?? [];
                    $inbetweenPhotos = $pFiles['photos_inbetween'] ?? [];
                    $afterPhotos = $pFiles['photos_after'] ?? ($pFiles['photos'] ?? []);
                    $bannerPhotos = $pFiles['photos_banner'] ?? [];
                    $stonePhotos = $pFiles['photos_stone'] ?? [];
                    $inaugurationPhotos = $pFiles['photos_inauguration'] ?? [];
                    $compDetails = $pFiles['completion_details'] ?? [];"""

# Target columns definition block (normalized to \n)
target_columns = """                            $columns = [
                                'before' => ['title' => 'Before', 'photos' => $beforePhotos],
                                'inbetween' => ['title' => 'In between', 'photos' => $inbetweenPhotos],
                                'after' => ['title' => 'After Completion', 'photos' => $afterPhotos],
                                'inauguration' => ['title' => 'Inauguration', 'photos' => $inaugurationPhotos],
                            ];"""

new_columns = """                            $columns = [
                                'before' => ['title' => 'Before Implementation', 'photos' => $beforePhotos],
                                'starting' => ['title' => 'Starting(foundation )', 'photos' => $startingPhotos],
                                'inbetween' => ['title' => 'In beteween project Implementation', 'photos' => $inbetweenPhotos],
                                'after' => ['title' => 'final Photo of Project', 'photos' => $afterPhotos],
                                'banner' => ['title' => 'photo of banner', 'photos' => $bannerPhotos],
                                'stone' => ['title' => 'photo of stone', 'photos' => $stonePhotos],
                                'inauguration' => ['title' => 'photo of inaguration', 'photos' => $inaugurationPhotos],
                            ];"""

# Normalize target strings to \n
target_variables = target_variables.replace("\r\n", "\n")
new_variables = new_variables.replace("\r\n", "\n")
target_columns = target_columns.replace("\r\n", "\n")
new_columns = new_columns.replace("\r\n", "\n")

for filename in os.listdir(directory):
    if filename.endswith(".blade.php"):
        filepath = os.path.join(directory, filename)
        with open(filepath, "r", encoding="utf-8") as f:
            content = f.read()
        
        normalized_content = content.replace("\r\n", "\n")
        
        has_vars = target_variables in normalized_content
        has_cols = target_columns in normalized_content
        
        if has_vars:
            normalized_content = normalized_content.replace(target_variables, new_variables)
        if has_cols:
            normalized_content = normalized_content.replace(target_columns, new_columns)
            
        if has_vars or has_cols:
            with open(filepath, "w", encoding="utf-8", newline="\r\n") as f:
                f.write(normalized_content)
            print(f"Processed: {filename} (vars: {has_vars}, cols: {has_cols})")
        else:
            print(f"Skipped: {filename}")
