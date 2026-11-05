<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Submission;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::orderBy('name')->pluck('name')->toArray();
        return response()->json($divisions);
    }

    public function store(Request $request)
    {
        $name = trim($request->input('name'));
        if (empty($name)) {
            return back()->with('error', 'Nama divisi tidak boleh kosong.');
        }

        $div = Division::firstOrCreate(['name' => $name]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Divisi ' . $name . ' berhasil ditambahkan!',
                'division' => $div->name
            ]);
        }

        return back()->with('success', 'Divisi ' . $name . ' berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $oldName = trim($request->input('oldName'));
        $newName = trim($request->input('newName'));

        if (empty($oldName) || empty($newName)) {
            return back()->with('error', 'Nama divisi lama dan baru tidak boleh kosong.');
        }

        $div = Division::where('name', $oldName)->first();
        if ($div) {
            $div->update(['name' => $newName]);
        } else {
            Division::create(['name' => $newName]);
        }

        // Cascade update in existing submissions
        $submissions = Submission::where('departemen', $oldName)->get();
        foreach ($submissions as $sub) {
            $data = $sub->data;
            if (isset($data['departemen'])) $data['departemen'] = $newName;
            if (isset($data['kategori'])) $data['kategori'] = $newName;
            $sub->update([
                'departemen' => $newName,
                'data' => $data,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Divisi berhasil diubah dari '{$oldName}' menjadi '{$newName}'!",
                'updatedSubmissions' => $submissions->count()
            ]);
        }

        return back()->with('success', "Divisi berhasil diubah dari '{$oldName}' menjadi '{$newName}'!");
    }

    public function destroy(Request $request, string $name)
    {
        $decoded = urldecode($name);
        Division::where('name', $decoded)->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Divisi ' . $decoded . ' berhasil dihapus!'
            ]);
        }

        return back()->with('success', 'Divisi ' . $decoded . ' berhasil dihapus!');
    }
}

