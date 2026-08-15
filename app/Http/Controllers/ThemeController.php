<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Subtheme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    private function canManageThemes($user)
    {
        if (!$user) {
            return false;
        }
        return $user->canManageMasterData();
    }

    private function canEditOrDelete($user)
    {
        if (!$user) {
            return false;
        }
        return $user->isSuperAdmin() || $user->isCoo();
    }

    public function index()
    {
        $themes = Theme::withCount('subthemes')->orderBy('name', 'asc')->get();
        $subthemes = Subtheme::with('theme')->orderBy('name', 'asc')->get();
        $canManage = $this->canManageThemes(auth()->user());
        $canEditOrDelete = $this->canEditOrDelete(auth()->user());

        return view('admin.themes', compact('themes', 'subthemes', 'canManage', 'canEditOrDelete'));
    }

    public function storeTheme(Request $request)
    {
        if (!$this->canManageThemes(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:themes,name'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ]);

        $data['status'] = $request->has('status') ? (int)$request->status : 1;

        Theme::create($data);

        return redirect()->route('themes.index')->with('success', 'Theme added successfully!');
    }

    public function updateTheme(Request $request, $id)
    {
        if (!$this->canEditOrDelete(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin and COO can edit or delete themes.');
        }

        $theme = Theme::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:themes,name,' . $id],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ]);

        $data['status'] = $request->has('status') ? (int)$request->status : 1;

        $theme->update($data);

        return redirect()->route('themes.index')->with('success', 'Theme updated successfully!');
    }

    public function destroyTheme($id)
    {
        if (!$this->canEditOrDelete(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin and COO can edit or delete themes.');
        }

        $theme = Theme::findOrFail($id);
        
        // Optionally delete child subthemes or handle linkage
        $theme->subthemes()->delete();
        $theme->delete();

        return redirect()->route('themes.index')->with('success', 'Theme and its associated subthemes deleted successfully.');
    }

    public function storeSubtheme(Request $request)
    {
        if (!$this->canManageThemes(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $data = $request->validate([
            'theme_id' => ['required', 'exists:themes,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ]);

        $data['status'] = $request->has('status') ? (int)$request->status : 1;

        Subtheme::create($data);

        return redirect()->route('themes.index')->with('success', 'Subtheme added successfully!');
    }

    public function updateSubtheme(Request $request, $id)
    {
        if (!$this->canEditOrDelete(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin and COO can edit or delete subthemes.');
        }

        $subtheme = Subtheme::findOrFail($id);

        $data = $request->validate([
            'theme_id' => ['required', 'exists:themes,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ]);

        $data['status'] = $request->has('status') ? (int)$request->status : 1;

        $subtheme->update($data);

        return redirect()->route('themes.index')->with('success', 'Subtheme updated successfully!');
    }

    public function destroySubtheme($id)
    {
        if (!$this->canEditOrDelete(auth()->user())) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin and COO can edit or delete subthemes.');
        }

        $subtheme = Subtheme::findOrFail($id);
        $subtheme->delete();

        return redirect()->route('themes.index')->with('success', 'Subtheme deleted successfully.');
    }
}
