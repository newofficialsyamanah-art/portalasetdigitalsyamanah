<?php

namespace App\Http\Controllers;

use App\Models\AppSystem;
use App\Models\GuideBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AppSystemController extends Controller
{
    /**
     * Display a listing of the registered systems
     */
    public function index()
    {
        AppSystem::ensureTableExists();
        $systems = AppSystem::orderBy('order_num')->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'systems' => $systems,
        ]);
    }

    /**
     * Store a newly created system in storage
     */
    public function store(Request $request)
    {
        AppSystem::ensureTableExists();

        $user = Auth::user();
        if ($user && !$user->can('manage systems') && $user->role !== 'superadmin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk menambah master sistem.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menambah master sistem.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:app_systems,name',
            'icon' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'order_num' => 'nullable|integer',
        ]);

        $featuresArray = [];
        if (!empty($validated['features'])) {
            $rawFeatures = preg_split('/[\r\n,]+/', $validated['features']);
            $featuresArray = array_values(array_filter(array_map('trim', $rawFeatures)));
        }

        if (empty($featuresArray)) {
            $featuresArray = [
                'Operasional Standar ' . $validated['name'],
                'Konfigurasi Pengguna',
                'Pencegahan Kendala',
            ];
        }

        $system = AppSystem::create([
            'name' => trim($validated['name']),
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?: '💻',
            'category' => $validated['category'] ?: 'SOP Operasional',
            'description' => $validated['description'] ?? '',
            'features' => $featuresArray,
            'order_num' => (int) ($validated['order_num'] ?? (AppSystem::max('order_num') + 1)),
            'is_active' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sistem '{$system->name}' berhasil ditambahkan ke master superadmin!",
                'system' => $system,
            ]);
        }

        return redirect('/?tab=superadmin&subTab=systems')->with('success', "Sistem '{$system->name}' berhasil ditambahkan ke master superadmin!");
    }

    /**
     * Update the specified system in storage
     */
    public function update(Request $request, int $id)
    {
        AppSystem::ensureTableExists();

        $user = Auth::user();
        if ($user && !$user->can('manage systems') && $user->role !== 'superadmin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk memperbarui master sistem.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk memperbarui master sistem.');
        }

        $system = AppSystem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:app_systems,name,' . $id,
            'icon' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'order_num' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $oldName = $system->name;
        $newName = trim($validated['name']);

        $featuresArray = [];
        if (!empty($validated['features'])) {
            $rawFeatures = preg_split('/[\r\n,]+/', $validated['features']);
            $featuresArray = array_values(array_filter(array_map('trim', $rawFeatures)));
        } else {
            $featuresArray = $system->features ?: [];
        }

        $system->update([
            'name' => $newName,
            'slug' => Str::slug($newName),
            'icon' => $validated['icon'] ?: ($system->icon ?: '💻'),
            'category' => $validated['category'] ?: ($system->category ?: 'SOP Operasional'),
            'description' => $validated['description'] ?? $system->description,
            'features' => $featuresArray,
            'order_num' => isset($validated['order_num']) ? (int) $validated['order_num'] : $system->order_num,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : $system->is_active,
        ]);

        // Cascade update system_name in GuideBook table if name changed
        if ($oldName !== $newName) {
            GuideBook::where('system_name', $oldName)->update(['system_name' => $newName]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sistem '{$system->name}' berhasil diperbarui!",
                'system' => $system,
            ]);
        }

        return redirect('/?tab=superadmin&subTab=systems')->with('success', "Sistem '{$system->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified system from storage
     */
    public function destroy(Request $request, int $id)
    {
        AppSystem::ensureTableExists();

        $user = Auth::user();
        if ($user && !$user->can('manage systems') && $user->role !== 'superadmin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk menghapus master sistem.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus master sistem.');
        }

        $system = AppSystem::find($id);

        if (!$system) {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Sistem sudah tidak ada atau telah dihapus.']);
            }
            return redirect('/?tab=superadmin&subTab=systems')->with('success', 'Sistem sudah tidak ada atau telah dihapus.');
        }

        $name = $system->name;
        $system->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sistem '{$name}' berhasil dihapus secara permanen dari master sistem!",
            ]);
        }

        return redirect('/?tab=superadmin&subTab=systems')->with('success', "Sistem '{$name}' berhasil dihapus secara permanen!");
    }

    /**
     * Clear all systems from master storage
     */
    public function clearAll(Request $request)
    {
        AppSystem::ensureTableExists();

        $user = Auth::user();
        if ($user && !$user->can('manage systems') && $user->role !== 'superadmin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk mengosongkan master sistem.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengosongkan master sistem.');
        }

        $count = AppSystem::count();
        AppSystem::truncate();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Seluruh master sistem ({$count} data) berhasil dibersihkan total!",
            ]);
        }

        return redirect('/?tab=superadmin&subTab=systems')->with('success', "Seluruh master sistem ({$count} data) berhasil dibersihkan total!");
    }
}
