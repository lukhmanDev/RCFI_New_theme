<?php

namespace App\Http\Controllers;

use App\Models\Cluster;
use Illuminate\Http\Request;

class ClusterController extends Controller
{
    private function canManageClusters($user)
    {
        if (!$user) {
            return false;
        }
        $designationLower = strtolower($user->designation ?? '');
        $isAdminDesignation = in_array($designationLower, ['admin', 'super admin', 'coo', 'hod']) || str_contains($designationLower, 'admin');

        return $user->isSuperAdmin() || $user->isCoo() || $user->isHod() || $user->hasAdminAccess() || $isAdminDesignation;
    }

    public function index()
    {
        $clusters = Cluster::orderBy('created_at', 'desc')->get();
        $canManage = $this->canManageClusters(auth()->user());
        $isSuperAdmin = auth()->user() && auth()->user()->isSuperAdmin();
        return view('admin.clusters', compact('clusters', 'canManage', 'isSuperAdmin'));
    }

    public function exportExcel()
    {
        if (auth()->user() && !auth()->user()->canDownloadExcel()) {
            return redirect()->back()->with('error', 'Users with role "Others" cannot download Excel exports.');
        }

        if (!$this->canManageClusters(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $clusters = Cluster::orderBy('created_at', 'desc')->get();

        $headers = [
            'Code', 'Cluster Name', 'Institution Name', 'Head of Institute', 'Head Contact No',
            'Place', 'Post Office', 'Village', 'Panchayath', 'District', 'State',
            'Contact No', 'Coordinator Name', 'Coordinator Contact', 'Remarks',
        ];

        $rows = [];
        foreach ($clusters as $c) {
            $rows[] = [
                $c->code,
                $c->name,
                $c->institution_name,
                $c->head_of_institution,
                $c->head_contact_number,
                $c->place,
                $c->po,
                $c->village,
                $c->panjayath,
                $c->dist,
                $c->state,
                $c->contact_no,
                $c->cordinator_name,
                $c->cordinator_contact_number,
                $c->remarks,
            ];
        }

        $filename = 'clusters_' . date('Y-m-d') . '.xls';
        return \App\Services\ExcelExportHelper::streamDownload($filename, $headers, $rows);
    }

    public function store(Request $request)
    {
        if (!$this->canManageClusters(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $data = $request->validate([
            'code'                      => ['nullable', 'string', 'max:255'],
            'name'                      => ['required', 'string', 'max:255'],
            'institution_name'          => ['nullable', 'string', 'max:255'],
            'head_of_institution'       => ['nullable', 'string', 'max:255'],
            'head_contact_number'       => ['nullable', 'string', 'max:255'],
            'place'                     => ['nullable', 'string', 'max:255'],
            'po'                        => ['nullable', 'string', 'max:255'],
            'village'                   => ['nullable', 'string', 'max:255'],
            'panjayath'                 => ['nullable', 'string', 'max:255'],
            'dist'                      => ['nullable', 'string', 'max:255'],
            'state'                     => ['nullable', 'string', 'max:255'],
            'contact_no'                => ['nullable', 'string', 'max:255'],
            'cordinator_name'           => ['nullable', 'string', 'max:255'],
            'cordinator_contact_number' => ['nullable', 'string', 'max:255'],
            'remarks'                   => ['nullable', 'string'],
        ]);

        Cluster::create($data);

        return redirect()->route('clusters.index')->with('success', 'Cluster registered successfully!');
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only Super Admin can edit clusters.');
        }

        $cluster = Cluster::findOrFail($id);

        $data = $request->validate([
            'code'                      => ['nullable', 'string', 'max:255'],
            'name'                      => ['required', 'string', 'max:255'],
            'institution_name'          => ['nullable', 'string', 'max:255'],
            'head_of_institution'       => ['nullable', 'string', 'max:255'],
            'head_contact_number'       => ['nullable', 'string', 'max:255'],
            'place'                     => ['nullable', 'string', 'max:255'],
            'po'                        => ['nullable', 'string', 'max:255'],
            'village'                   => ['nullable', 'string', 'max:255'],
            'panjayath'                 => ['nullable', 'string', 'max:255'],
            'dist'                      => ['nullable', 'string', 'max:255'],
            'state'                     => ['nullable', 'string', 'max:255'],
            'contact_no'                => ['nullable', 'string', 'max:255'],
            'cordinator_name'           => ['nullable', 'string', 'max:255'],
            'cordinator_contact_number' => ['nullable', 'string', 'max:255'],
            'remarks'                   => ['nullable', 'string'],
        ]);

        $cluster->update($data);

        return redirect()->route('clusters.index')->with('success', 'Cluster updated successfully!');
    }

    public function destroy($id)
    {
        if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only Super Admin can delete clusters.');
        }

        $cluster = Cluster::findOrFail($id);
        $cluster->delete();

        return redirect()->route('clusters.index')->with('success', 'Cluster deleted successfully.');
    }
}
