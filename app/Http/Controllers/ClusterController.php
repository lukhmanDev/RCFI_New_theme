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
        // Super Admin (1), COO (2), HOD (4), Project Manager (3), Engineer (6)
        return in_array($user->role, [1, 2, 3, 4, 6]) || in_array(strtolower($user->designation ?? ''), ['project manager', 'engineer', 'coo', 'hod']);
    }

    public function index()
    {
        $clusters = Cluster::orderBy('created_at', 'desc')->get();
        $canManage = $this->canManageClusters(auth()->user());
        return view('admin.clusters', compact('clusters', 'canManage'));
    }

    public function store(Request $request)
    {
        if (!$this->canManageClusters(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'panjayath' => ['nullable', 'string', 'max:255'],
            'dist' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:255'],
            'cordinator_name' => ['nullable', 'string', 'max:255'],
            'cordinator_contact_number' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        Cluster::create($data);

        return redirect()->route('clusters.index')->with('success', 'Cluster registered successfully!');
    }

    public function update(Request $request, $id)
    {
        if (!$this->canManageClusters(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $cluster = Cluster::findOrFail($id);

        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'panjayath' => ['nullable', 'string', 'max:255'],
            'dist' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:255'],
            'cordinator_name' => ['nullable', 'string', 'max:255'],
            'cordinator_contact_number' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        $cluster->update($data);

        return redirect()->route('clusters.index')->with('success', 'Cluster updated successfully!');
    }

    public function destroy($id)
    {
        if (!$this->canManageClusters(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $cluster = Cluster::findOrFail($id);
        $cluster->delete();

        return redirect()->route('clusters.index')->with('success', 'Cluster deleted successfully.');
    }
}
