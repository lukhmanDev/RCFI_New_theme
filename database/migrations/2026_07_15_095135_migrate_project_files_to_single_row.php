<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('project_files', 'name')) {
            // 1. Add new columns
            Schema::table('project_files', function (Blueprint $table) {
                $table->text('photos')->nullable()->after('project_type');
                $table->text('photos_before')->nullable()->after('photos');
                $table->text('photos_inbetween')->nullable()->after('photos_before');
                $table->text('photos_after')->nullable()->after('photos_inbetween');
                $table->text('photos_inauguration')->nullable()->after('photos_after');
            });

            // 2. Fetch existing data into memory
            $existing = DB::table('project_files')->get();
            $grouped = $existing->groupBy(function ($item) {
                return $item->project_id . '-' . $item->project_type;
            });

            // 3. Clean up the table (delete all old rows and drop name/path columns)
            DB::table('project_files')->delete();
            Schema::table('project_files', function (Blueprint $table) {
                $table->dropColumn(['name', 'path']);
            });

            // 4. Insert migrated data
            foreach ($grouped as $key => $records) {
                $first = $records->first();
                
                $data = [
                    'project_id' => $first->project_id,
                    'project_type' => $first->project_type,
                    'photos' => null,
                    'photos_before' => null,
                    'photos_inbetween' => null,
                    'photos_after' => null,
                    'photos_inauguration' => null,
                    'created_at' => $first->created_at ?? now(),
                    'updated_at' => $first->updated_at ?? now(),
                ];

                foreach ($records as $record) {
                    if (in_array($record->name, ['photos', 'photos_before', 'photos_inbetween', 'photos_after', 'photos_inauguration'])) {
                        $data[$record->name] = $record->path;
                    }
                }

                // Insert a new combined row
                DB::table('project_files')->insert($data);
            }

            // 5. Drop old index and add unique index
            Schema::table('project_files', function (Blueprint $table) {
                try {
                    $table->dropIndex('project_files_project_id_project_type_index');
                } catch (\Exception $e) {
                    // Ignore if it fails or has a different name
                }
                
                $table->unique(['project_id', 'project_type'], 'project_files_project_id_project_type_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('project_files', 'name')) {
            // 1. Re-add name and path columns
            Schema::table('project_files', function (Blueprint $table) {
                $table->string('name')->nullable()->after('project_type');
                $table->text('path')->nullable()->after('name');
            });

            // 2. Fetch existing data
            $existing = DB::table('project_files')->get();

            // 3. Clear the table
            DB::table('project_files')->delete();

            // 4. Migrate data back
            foreach ($existing as $record) {
                $fields = [
                    'photos' => $record->photos,
                    'photos_before' => $record->photos_before,
                    'photos_inbetween' => $record->photos_inbetween,
                    'photos_after' => $record->photos_after,
                    'photos_inauguration' => $record->photos_inauguration,
                ];

                foreach ($fields as $name => $path) {
                    if ($path !== null) {
                        DB::table('project_files')->insert([
                            'project_id' => $record->project_id,
                            'project_type' => $record->project_type,
                            'name' => $name,
                            'path' => $path,
                            'created_at' => $record->created_at,
                            'updated_at' => $record->updated_at,
                        ]);
                    }
                }
            }

            // 5. Drop the new columns
            Schema::table('project_files', function (Blueprint $table) {
                $table->dropColumn(['photos', 'photos_before', 'photos_inbetween', 'photos_after', 'photos_inauguration']);
            });

            // 6. Revert unique index to normal index
            Schema::table('project_files', function (Blueprint $table) {
                try {
                    $table->dropUnique('project_files_project_id_project_type_unique');
                } catch (\Exception $e) {
                    // Ignore
                }
                $table->index(['project_id', 'project_type'], 'project_files_project_id_project_type_index');
            });
        }
    }
};
