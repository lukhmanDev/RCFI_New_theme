<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    private function canManageContractors($user)
    {
        if (!$user) {
            return false;
        }
        return ($user->role == 3 || $user->role == 1 || $user->role == 6 || strtolower($user->designation) === 'project manager' || strtolower($user->designation) === 'engineer');
    }

    public function index()
    {
        $contractors = Contractor::orderBy('created_at', 'desc')->get();
        $canManage = $this->canManageContractors(auth()->user());
        return view('admin.contractors', compact('contractors', 'canManage'));
    }

    public function store(Request $request)
    {
        if (!$this->canManageContractors(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin, Project Manager, and Engineer can add contractors.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Contractor::create($data);

        return redirect()->route('contractors.index')->with('success', 'Contractor registered successfully!');
    }

    public function update(Request $request, $id)
    {
        if (!$this->canManageContractors(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin, Project Manager, and Engineer can edit contractors.');
        }

        $contractor = Contractor::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $contractor->update($data);

        return redirect()->route('contractors.index')->with('success', 'Contractor details updated successfully!');
    }

    public function destroy($id)
    {
        if (!$this->canManageContractors(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin, Project Manager, and Engineer can delete contractors.');
        }

        $contractor = Contractor::findOrFail($id);
        $contractor->delete();

        return redirect()->route('contractors.index')->with('success', 'Contractor deleted successfully.');
    }
}
