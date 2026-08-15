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
        return $user->canManageMasterData();
    }

    private function canEditOrDeleteContractor($user)
    {
        if (!$user) {
            return false;
        }
        return $user->isSuperAdmin() || $user->isCoo();
    }

    public function index()
    {
        $contractors = Contractor::orderBy('created_at', 'desc')->get();
        $canManage = $this->canManageContractors(auth()->user());
        $canEditOrDelete = $this->canEditOrDeleteContractor(auth()->user());
        return view('admin.contractors', compact('contractors', 'canManage', 'canEditOrDelete'));
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
        if (!$this->canEditOrDeleteContractor(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin and COO can edit or delete contractors.');
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
        if (!$this->canEditOrDeleteContractor(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin and COO can edit or delete contractors.');
        }

        $contractor = Contractor::findOrFail($id);
        $contractor->delete();

        return redirect()->route('contractors.index')->with('success', 'Contractor deleted successfully.');
    }
}
