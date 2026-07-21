<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonorController extends Controller
{
    public function index()
    {
        $donors = Donor::orderBy('created_at', 'desc')->get();
        return view('admin.donors', compact('donors'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Unauthorized action. Only administrators can add partners.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'type_of_partner' => ['required', 'string'],
            'type_of_fund' => ['required', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'support_initiated_at' => ['nullable', 'string', 'max:255'], // YYYY-MM or Date string
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Max 2MB
        ]);

        if ($request->hasFile('image')) {
            // Save inside storage/app/public/donors
            $path = $request->file('image')->store('donors', 'public');
            $data['image_path'] = $path;
        }

        unset($data['image']);

        Donor::create($data);

        return redirect()->route('donors.index')->with('success', 'Donor / Partner registered successfully!');
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Unauthorized action. Only administrators can edit partners.');
        }

        $donor = Donor::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'type_of_partner' => ['required', 'string'],
            'type_of_fund' => ['required', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'support_initiated_at' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            // Clean up old image if it exists
            if ($donor->image_path && Storage::disk('public')->exists($donor->image_path)) {
                Storage::disk('public')->delete($donor->image_path);
            }

            // Save new image
            $path = $request->file('image')->store('donors', 'public');
            $data['image_path'] = $path;
        }

        unset($data['image']);

        $donor->update($data);

        return redirect()->route('donors.index')->with('success', 'Donor / Partner details updated successfully!');
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Unauthorized action. Only administrators can delete partners.');
        }

        $donor = Donor::findOrFail($id);

        // Delete image file from storage if exists
        if ($donor->image_path && Storage::disk('public')->exists($donor->image_path)) {
            Storage::disk('public')->delete($donor->image_path);
        }

        $donor->delete();

        return redirect()->route('donors.index')->with('success', 'Donor / Partner details deleted successfully.');
    }
}
