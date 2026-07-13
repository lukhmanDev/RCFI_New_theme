<?php

namespace App\Traits;

use App\Models\ProjectFile;
use App\Models\ProjectContractor;
use App\Models\ProjectExpense;
use App\Models\ProjectDocument;
use App\Models\ProjectStatus;
use App\Models\ProjectCommunityContribution;
use App\Models\ProjectCompletionDetail;
use Illuminate\Support\Facades\DB;

trait HasProjectColumns
{
    protected $tempFilesToSave;
    protected $tempMaterialsToSave;
    protected $tempExpensesToSave;

    // Define polymorphic relations
    public function projectFiles()
    {
        return $this->morphMany(ProjectFile::class, 'project');
    }

    public function projectDocument()
    {
        return $this->morphOne(ProjectDocument::class, 'project');
    }

    public function projectStatus()
    {
        return $this->morphOne(ProjectStatus::class, 'project');
    }

    public function projectContractors()
    {
        return $this->morphMany(ProjectContractor::class, 'project');
    }

    public function projectExpenses()
    {
        return $this->morphMany(ProjectExpense::class, 'project');
    }

    public function projectCommunityContributions()
    {
        return $this->morphMany(ProjectCommunityContribution::class, 'project');
    }

    public function projectCompletionDetail()
    {
        return $this->morphOne(ProjectCompletionDetail::class, 'project');
    }

    // Accessor for $project->files
    public function getFilesAttribute()
    {
        $files = [];

        // 1. Fetch checklist and uploaded files from projectDocument
        $docRecord = $this->projectDocument;
        if ($docRecord) {
            foreach (ProjectDocument::$docColumnMap as $docName => $column) {
                $val = $docRecord->$column;
                if ($val && $val !== '0') {
                    $files[$docName] = $val;
                }
            }
        }

        // 2. Fetch contractors
        $files['contractors'] = $this->projectContractors()->with('contractor')->get()->map(function($c) {
            return [
                'contractor_id' => $c->contractor_id,
                'contractor_name' => $c->contractor ? $c->contractor->name : $c->contractor_name,
                'phone' => $c->contractor ? $c->contractor->phone : $c->phone,
                'company_name' => $c->contractor ? $c->contractor->company_name : $c->company_name,
                'address' => $c->contractor ? $c->contractor->address : $c->address,
                'type_of_contract' => $c->type_of_contract,
                'purpose_of_contract' => $c->purpose_of_contract,
            ];
        })->toArray();

        // 3. Fetch photos
        $photosFile = $this->projectFiles()->where('name', 'photos')->first();
        $files['photos'] = $photosFile ? json_decode($photosFile->path, true) : [];

        $beforePhotosFile = $this->projectFiles()->where('name', 'photos_before')->first();
        $files['photos_before'] = $beforePhotosFile ? json_decode($beforePhotosFile->path, true) : [];

        $inbetweenPhotosFile = $this->projectFiles()->where('name', 'photos_inbetween')->first();
        $files['photos_inbetween'] = $inbetweenPhotosFile ? json_decode($inbetweenPhotosFile->path, true) : [];

        $afterPhotosFile = $this->projectFiles()->where('name', 'photos_after')->first();
        $files['photos_after'] = $afterPhotosFile ? json_decode($afterPhotosFile->path, true) : [];

        $inaugurationPhotosFile = $this->projectFiles()->where('name', 'photos_inauguration')->first();
        $files['photos_inauguration'] = $inaugurationPhotosFile ? json_decode($inaugurationPhotosFile->path, true) : [];

        // 4. Fetch community contributions with self-healing legacy auto-migration
        $contribs = [];
        try {
            $hasNewContribs = $this->projectCommunityContributions()->exists();
            if ($hasNewContribs) {
                $contribs = $this->projectCommunityContributions()->get()->map(function($c) {
                    return [
                        'item' => $c->item,
                        'amount' => (float)$c->amount
                    ];
                })->toArray();
            } else {
                $legacy = is_string($this->community_contributions) 
                    ? json_decode($this->community_contributions, true) 
                    : $this->community_contributions;
                if (!empty($legacy) && is_array($legacy)) {
                    $contribs = $legacy;
                    foreach ($legacy as $l) {
                        $this->projectCommunityContributions()->create([
                            'item' => $l['item'] ?? '',
                            'amount' => (float)($l['amount'] ?? 0)
                        ]);
                    }
                    DB::table($this->getTable())->where('id', $this->id)->update(['community_contributions' => null]);
                }
            }
        } catch (\Exception $e) {
            // Fallback to legacy in case migrations haven't run or fail in test environment
            $contribs = is_string($this->community_contributions) 
                ? json_decode($this->community_contributions, true) 
                : $this->community_contributions;
        }
        $files['community_contributions'] = $contribs ?: [];

        // 5. Fetch completion details with self-healing legacy auto-migration
        $details = [];
        try {
            $newDetail = $this->projectCompletionDetail;
            if ($newDetail) {
                $details = [
                    'total_project_cost' => (float)$newDetail->total_project_cost,
                    'total_amount' => (float)$newDetail->total_amount,
                    'amount_paid_by_donor' => (float)$newDetail->amount_paid_by_donor,
                    'community_contribution' => (float)$newDetail->community_contribution,
                    'any_other' => (float)$newDetail->any_other,
                    'deductions' => (float)$newDetail->deductions,
                    'handover_date' => $newDetail->handover_date,
                    'handover_remarks' => $newDetail->handover_remarks
                ];
            } else {
                $legacy = is_string($this->completion_details) 
                    ? json_decode($this->completion_details, true) 
                    : $this->completion_details;
                if (!empty($legacy) && is_array($legacy)) {
                    $details = $legacy;
                    $this->projectCompletionDetail()->create([
                        'total_project_cost' => $legacy['total_project_cost'] ?? null,
                        'total_amount' => $legacy['total_amount'] ?? null,
                        'amount_paid_by_donor' => $legacy['amount_paid_by_donor'] ?? null,
                        'community_contribution' => $legacy['community_contribution'] ?? null,
                        'any_other' => $legacy['any_other'] ?? null,
                        'deductions' => $legacy['deductions'] ?? null,
                        'handover_date' => $legacy['handover_date'] ?? null,
                        'handover_remarks' => $legacy['handover_remarks'] ?? null
                    ]);
                    DB::table($this->getTable())->where('id', $this->id)->update(['completion_details' => null]);
                }
            }
        } catch (\Exception $e) {
            // Fallback to legacy
            $details = is_string($this->completion_details) 
                ? json_decode($this->completion_details, true) 
                : $this->completion_details;
        }
        $files['completion_details'] = $details ?: [];

        return $files;
    }

    // Accessor for checklist documents and files with their timestamps
    public function getFilesWithTimestampsAttribute()
    {
        return $this->projectDocument;
    }

    // Accessors for project_phase and project_phase_custom from projectStatus record
    public function getProjectPhaseAttribute()
    {
        return $this->projectStatus ? ($this->projectStatus->status ?? '') : '';
    }

    public function getProjectPhaseCustomAttribute()
    {
        return $this->projectStatus ? $this->projectStatus->status_custom : null;
    }



    // Mutator for $project->files
    public function setFilesAttribute($value)
    {
        if (is_array($value)) {
            $this->tempFilesToSave = $value;
        }
    }

    // Accessor for $project->materials
    public function getMaterialsAttribute()
    {
        return $this->projectExpenses()
            ->where('type', 'material')
            ->get()
            ->map(function($m) {
                return [
                    'material' => $m->expense_name,
                    'amount' => (float)$m->amount,
                ];
            })
            ->toArray();
    }

    // Mutator for $project->materials
    public function setMaterialsAttribute($value)
    {
        if (is_array($value)) {
            $this->tempMaterialsToSave = $value;
        }
    }

    // Accessor for $project->expenses
    public function getExpensesAttribute()
    {
        return $this->projectExpenses()
            ->where('type', 'spent')
            ->get()
            ->map(function($e) {
                return [
                    'expense_name' => $e->expense_name,
                    'quantity' => $e->quantity,
                    'amount' => (float)$e->amount,
                ];
            })
            ->toArray();
    }

    // Mutator for $project->expenses
    public function setExpensesAttribute($value)
    {
        if (is_array($value)) {
            $this->tempExpensesToSave = $value;
        }
    }

    // Boot the trait and register the saved event listener
    public static function bootHasProjectColumns()
    {
        // Cascade-delete all related data when a project is deleted
        static::deleting(function ($model) {
            // 1. Delete project status record
            $model->projectStatus()->delete();

            // 2. Delete project document record (checklist)
            $model->projectDocument()->delete();

            // 3. Delete project files (photos etc.) — also remove physical files from storage
            $model->projectFiles()->each(function ($file) {
                if ($file->name === 'photos') {
                    $photos = json_decode($file->path, true) ?? [];
                    foreach ($photos as $photoPath) {
                        $fullPath = public_path($photoPath);
                        if (file_exists($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                }
                $file->delete();
            });

            // 4. Delete project contractors
            $model->projectContractors()->delete();

            // 5. Delete project expenses (materials + spent)
            $model->projectExpenses()->delete();
        });

        // Automatically create projectDocument and projectStatus records when a project is created
        static::created(function ($model) {
            $insertData = [];
            foreach (ProjectDocument::$docColumnMap as $docName => $column) {
                $insertData[$column] = '0';
                $insertData[$column . '_ticked_at'] = null;
            }
            $model->projectDocument()->create($insertData);

            $model->projectStatus()->create([
                'status' => null,
                'status_custom' => null,
            ]);
        });

        static::saved(function ($model) {
            // 1. Save files if tempFilesToSave is set
            if (isset($model->tempFilesToSave)) {
                $value = $model->tempFilesToSave;

                // Sync photos
                if (isset($value['photos'])) {
                    $model->projectFiles()->where('name', 'photos')->delete();
                    $model->projectFiles()->create([
                        'name' => 'photos',
                        'path' => json_encode($value['photos']),
                    ]);
                }

                if (isset($value['photos_before'])) {
                    $model->projectFiles()->where('name', 'photos_before')->delete();
                    $model->projectFiles()->create([
                        'name' => 'photos_before',
                        'path' => json_encode($value['photos_before']),
                    ]);
                }

                if (isset($value['photos_inbetween'])) {
                    $model->projectFiles()->where('name', 'photos_inbetween')->delete();
                    $model->projectFiles()->create([
                        'name' => 'photos_inbetween',
                        'path' => json_encode($value['photos_inbetween']),
                    ]);
                }

                if (isset($value['photos_after'])) {
                    $model->projectFiles()->where('name', 'photos_after')->delete();
                    $model->projectFiles()->create([
                        'name' => 'photos_after',
                        'path' => json_encode($value['photos_after']),
                    ]);
                }

                if (isset($value['photos_inauguration'])) {
                    $model->projectFiles()->where('name', 'photos_inauguration')->delete();
                    $model->projectFiles()->create([
                        'name' => 'photos_inauguration',
                        'path' => json_encode($value['photos_inauguration']),
                    ]);
                }

                // Sync contractors
                if (isset($value['contractors'])) {
                    $model->projectContractors()->delete();
                    foreach ($value['contractors'] as $c) {
                        $cId = $c['contractor_id'] ?? null;
                        $cName = $c['contractor_name'] ?? '';
                        $cPhone = $c['phone'] ?? null;
                        $cCompany = $c['company_name'] ?? null;
                        $cAddress = $c['address'] ?? null;

                        if ($cId) {
                            $contractor = \App\Models\Contractor::find($cId);
                            if ($contractor) {
                                $cName = $contractor->name;
                                $cPhone = $contractor->phone;
                                $cCompany = $contractor->company_name;
                                $cAddress = $contractor->address;
                            }
                        }

                        $model->projectContractors()->create([
                            'contractor_id' => $cId,
                            'contractor_name' => $cName,
                            'phone' => $cPhone,
                            'company_name' => $cCompany,
                            'address' => $cAddress,
                            'type_of_contract' => $c['type_of_contract'] ?? null,
                            'purpose_of_contract' => $c['purpose_of_contract'] ?? null,
                        ]);
                    }
                }

                // Sync community_contributions and completion_details via polymorphic relations
                if (array_key_exists('community_contributions', $value)) {
                    $model->projectCommunityContributions()->delete();
                    if (is_array($value['community_contributions'])) {
                        foreach ($value['community_contributions'] as $c) {
                            $model->projectCommunityContributions()->create([
                                'item' => $c['item'] ?? '',
                                'amount' => (float)($c['amount'] ?? 0.00)
                            ]);
                        }
                    }
                }
                if (array_key_exists('completion_details', $value)) {
                    $comp = $value['completion_details'];
                    if (is_array($comp)) {
                        $model->projectCompletionDetail()->updateOrCreate([], [
                            'total_project_cost' => $comp['total_project_cost'] ?? null,
                            'total_amount' => $comp['total_amount'] ?? null,
                            'amount_paid_by_donor' => $comp['amount_paid_by_donor'] ?? null,
                            'community_contribution' => $comp['community_contribution'] ?? null,
                            'any_other' => $comp['any_other'] ?? null,
                            'deductions' => $comp['deductions'] ?? null,
                            'handover_date' => $comp['handover_date'] ?? null,
                            'handover_remarks' => $comp['handover_remarks'] ?? null
                        ]);
                    }
                }

                unset($model->tempFilesToSave);
            }

            // 2. Save materials if tempMaterialsToSave is set
            if (isset($model->tempMaterialsToSave)) {
                $model->projectExpenses()->where('type', 'material')->delete();
                foreach ($model->tempMaterialsToSave as $m) {
                    $model->projectExpenses()->create([
                        'expense_name' => $m['material'] ?? '',
                        'quantity' => 1,
                        'amount' => $m['amount'] ?? 0,
                        'type' => 'material',
                    ]);
                }
                unset($model->tempMaterialsToSave);
            }

            // 3. Save expenses if tempExpensesToSave is set
            if (isset($model->tempExpensesToSave)) {
                $model->projectExpenses()->where('type', 'spent')->delete();
                foreach ($model->tempExpensesToSave as $e) {
                    $model->projectExpenses()->create([
                        'expense_name' => $e['expense_name'] ?? '',
                        'quantity' => $e['quantity'] ?? 1,
                        'amount' => $e['amount'] ?? 0,
                        'type' => 'spent',
                    ]);
                }
                unset($model->tempExpensesToSave);
            }

            try {
                event(new \App\Events\ProjectUpdated($model->id, $model->type_of_project, auth()->id()));
            } catch (\Exception $e) {
                // Avoid blocking DB save if broadcast driver is not configured or offline
            }
        });
    }
}
