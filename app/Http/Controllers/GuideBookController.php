<?php

namespace App\Http\Controllers;

use App\Models\GuideBook;
use App\Services\GuideBookService;
use App\Services\SimplePdfBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GuideBookController extends Controller
{
    public function store(Request $request)
    {
        GuideBook::ensureTableExists();
        $user = Auth::user();

        if ($user && !$user->can('manage guidebooks') && $user->role !== 'superadmin') {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk mengunggah / membuat Guide Book.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengunggah / membuat Guide Book.');
        }

        $validated = $request->validate([
            'target_division' => 'nullable|string|max:100',
            'target_role' => 'nullable|string|max:100',
            'system_name' => 'required|string|max:100',
            'feature_name' => 'nullable|string|max:150',
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'version' => 'nullable|string|max:20',
            'author' => 'nullable|string|max:100',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'document_url' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'is_pinned' => 'nullable|boolean',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720',
        ]);

        // Handle target division
        $targetDivision = $validated['target_division'] ?? $request->input('target_division', 'Semua Divisi');
        if (($targetDivision === 'Kustom / Divisi Lainnya' || empty($targetDivision)) && $request->filled('custom_division')) {
            $targetDivision = trim($request->input('custom_division'));
        }
        if (empty($targetDivision)) {
            $targetDivision = 'Semua Divisi';
        }

        // Handle custom role or system if provided
        $targetRole = $validated['target_role'] ?? 'Semua Karyawan (Umum)';
        if (($targetRole === 'Kustom / Peran Lainnya' || empty($targetRole)) && $request->filled('custom_role')) {
            $targetRole = trim($request->input('custom_role'));
        }

        $systemName = $validated['system_name'];
        if (($systemName === 'Kustom / Sistem Lainnya' || empty($systemName)) && $request->filled('custom_system')) {
            $systemName = trim($request->input('custom_system'));
        }

        // Auto-infer category if not provided
        $category = $validated['category'] ?? null;
        if (empty($category)) {
            $sys = strtolower($systemName);
            if (str_contains($sys, 'dxdiag') || str_contains($sys, 'pc') || str_contains($sys, 'laptop')) {
                $category = 'Hardware';
            } elseif (str_contains($sys, 'erp') || str_contains($sys, 'api')) {
                $category = 'ERP & API';
            } elseif (str_contains($sys, 'mikrotik') || str_contains($sys, 'hotspot') || str_contains($sys, 'jaringan') || str_contains($sys, 'ip')) {
                $category = 'Jaringan';
            } elseif (str_contains($sys, 'phone') || str_contains($sys, 'sales') || str_contains($sys, 'smartphone') || str_contains($sys, 'akun')) {
                $category = 'Sales & Akun';
            } elseif (str_contains($sys, 'sheet') || str_contains($sys, 'apps script') || str_contains($sys, 'gas')) {
                $category = 'Integrasi Data';
            } elseif (str_contains($sys, 'rbac') || str_contains($sys, 'keamanan') || str_contains($sys, 'portal')) {
                $category = 'Keamanan';
            } else {
                $category = 'SOP Operasional';
            }
        }

        $tagsArray = [];
        if (!empty($validated['tags'])) {
            $tagsArray = array_map('trim', explode(',', $validated['tags']));
        }

        $filePath = null;
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $dir = storage_path('app/guidebooks');
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            $cleanName = 'upload_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $file->move($dir, $cleanName);
            $filePath = 'guidebooks/' . $cleanName;
        }

        // Auto-generate rich content if user didn't write manual content and didn't upload PDF
        $content = $validated['content'] ?? '';
        if (empty($content) && !$filePath) {
            $feat = $validated['feature_name'] ?: 'Operasional Fitur';
            $content = "# SOP {$feat}\n\n";
            $content .= "Buku panduan standar operasional untuk sistem **{$systemName}** yang ditujukan khusus bagi pengguna berwenang: **{$targetRole}**.\n\n";
            $content .= "---\n\n";
            $content .= "### 1. Tujuan & Ruang Lingkup\n";
            $content .= "- Mengatur tata cara operasional modul {$feat} secara terstandarisasi, aman, dan efisien.\n";
            $content .= "- Memastikan seluruh personel dengan peran {$targetRole} memahami batasan hak akses dan alur kerja yang benar.\n\n";
            $content .= "### 2. Prosedur & Langkah Kerja Standar\n";
            $content .= "1. Akses menu sistem terkait pada antarmuka Portal Aset Digital Syamanah.\n";
            $content .= "2. Buka modul {$feat} dan verifikasi kelengkapan data perangkat / akun.\n";
            $content .= "3. Lakukan tindakan operasional sesuai petunjuk teknis perusahaan.\n";
            $content .= "4. Pastikan status pencatatan telah diperbarui dan data tersinkronisasi sempurna.\n\n";
            $content .= "> [!NOTE] Perhatian: Segera hubungi Tim IT Syamanah apabila menemukan anomali teknis atau galat integrasi sistem.";
        }

        $guide = GuideBook::create([
            'target_division' => $targetDivision,
            'target_role' => $targetRole,
            'system_name' => $systemName,
            'feature_name' => $validated['feature_name'] ?? null,
            'title' => $validated['title'],
            'category' => $category,
            'version' => $validated['version'] ?: 'v1.0',
            'author' => $validated['author'] ?: ($user ? $user->name : 'IT Syamanah'),
            'summary' => $validated['summary'] ?? ($validated['title'] . ' untuk ' . $validated['target_role']),
            'content' => $content,
            'document_url' => $validated['document_url'] ?? null,
            'file_path' => $filePath,
            'tags' => $tagsArray,
            'is_pinned' => $request->boolean('is_pinned'),
            'views_count' => 0,
        ]);

        // If no PDF uploaded, auto-generate official PDF immediately
        if (!$filePath) {
            $generatedPath = SimplePdfBuilder::generateForGuide($guide);
            $relPath = 'guidebooks/' . basename($generatedPath);
            $guide->update(['file_path' => $relPath]);
        }

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Buku Panduan PDF berhasil disimpan & diproteksi!',
                'guide' => $guide
            ]);
        }

        return redirect('/?tab=guides')
            ->with('success', 'Buku Panduan PDF "' . $guide->title . '" berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        GuideBook::ensureTableExists();
        $user = Auth::user();

        if ($user && !$user->can('manage guidebooks') && $user->role !== 'superadmin') {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk mengubah Guide Book.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengubah Guide Book.');
        }

        // Safe lookup: find by id, then by title, or fallback to store
        $guide = GuideBook::find($id);
        if (!$guide && $request->filled('title')) {
            $guide = GuideBook::where('title', $request->input('title'))->first();
        }
        if (!$guide) {
            return $this->store($request);
        }

        $validated = $request->validate([
            'target_division' => 'nullable|string|max:100',
            'target_role' => 'nullable|string|max:100',
            'system_name' => 'required|string|max:100',
            'feature_name' => 'nullable|string|max:150',
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'version' => 'nullable|string|max:20',
            'author' => 'nullable|string|max:100',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'document_url' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'is_pinned' => 'nullable|boolean',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720',
        ]);

        $tagsArray = $guide->tags ?? [];
        if (isset($validated['tags'])) {
            $tagsArray = array_map('trim', explode(',', $validated['tags']));
        }

        // Handle target division
        $targetDivision = $validated['target_division'] ?? $request->input('target_division', $guide->target_division ?: 'Semua Divisi');
        if (($targetDivision === 'Kustom / Divisi Lainnya' || empty($targetDivision)) && $request->filled('custom_division')) {
            $targetDivision = trim($request->input('custom_division'));
        }

        // Handle custom role or system if provided
        $targetRole = $validated['target_role'] ?? ($guide->target_role ?: 'Semua Karyawan (Umum)');
        if (($targetRole === 'Kustom / Peran Lainnya' || empty($targetRole)) && $request->filled('custom_role')) {
            $targetRole = trim($request->input('custom_role'));
        }

        $systemName = $validated['system_name'];
        if (($systemName === 'Kustom / Sistem Lainnya' || empty($systemName)) && $request->filled('custom_system')) {
            $systemName = trim($request->input('custom_system'));
        }

        $filePath = $guide->file_path;
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $dir = storage_path('app/guidebooks');
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            $cleanName = 'upload_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
            $file->move($dir, $cleanName);
            $filePath = 'guidebooks/' . $cleanName;
        }

        $guide->update([
            'target_division' => $targetDivision ?: 'Semua Divisi',
            'target_role' => $targetRole,
            'system_name' => $systemName,
            'feature_name' => $validated['feature_name'] ?? $guide->feature_name,
            'title' => $validated['title'],
            'category' => $validated['category'] ?: $guide->category,
            'version' => $validated['version'] ?: $guide->version,
            'author' => $validated['author'] ?: $guide->author,
            'summary' => $validated['summary'] ?? $guide->summary,
            'content' => $validated['content'] ?? $guide->content,
            'document_url' => $validated['document_url'] ?? $guide->document_url,
            'file_path' => $filePath,
            'tags' => $tagsArray,
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        // Re-generate PDF if content updated and no manual file uploaded
        if (!$request->hasFile('pdf_file')) {
            $generatedPath = SimplePdfBuilder::generateForGuide($guide);
            $relPath = 'guidebooks/' . basename($generatedPath);
            $guide->update(['file_path' => $relPath]);
        }

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Buku Panduan berhasil diperbarui!',
                'guide' => $guide
            ]);
        }

        return redirect('/?tab=guides')
            ->with('success', 'Buku Panduan "' . $guide->title . '" berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && !$user->can('delete records') && !$user->can('manage guidebooks') && $user->role !== 'superadmin') {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk menghapus Guide Book.'], 403);
            }
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus Guide Book.');
        }

        $guide = GuideBook::find($id);
        if (!$guide) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Dokumen sudah tidak ada.']);
            }
            return redirect('/?tab=guides');
        }
        $title = $guide->title;

        if ($guide->file_path) {
            if (file_exists(storage_path('app/' . $guide->file_path))) {
                @unlink(storage_path('app/' . $guide->file_path));
            } elseif (file_exists(public_path($guide->file_path))) {
                @unlink(public_path($guide->file_path));
            }
        }

        $guide->delete();

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Buku Panduan berhasil dihapus!'
            ]);
        }

        return redirect('/?tab=guides')
            ->with('success', 'Buku Panduan "' . $title . '" berhasil dihapus!');
    }

    public function show(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && !$user->can('view guidebooks') && !$user->can('manage guidebooks') && $user->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk melihat Guide Book.'], 403);
        }

        $guide = GuideBook::find($id);
        if (!$guide) {
            return response()->json(['success' => false, 'message' => 'Dokumen tidak ditemukan'], 404);
        }
        $guide->increment('views_count');

        return response()->json([
            'success' => true,
            'guide' => $guide
        ]);
    }

    /**
     * Secure Stream PDF Endpoint (Controlled DRM Delivery)
     */
    public function streamPdf(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && !$user->can('view guidebooks') && !$user->can('manage guidebooks') && $user->role !== 'superadmin') {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk membaca dokumen PDF ini.'], 403);
            }
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk membaca dokumen PDF ini.');
        }

        $guide = GuideBook::find($id);
        if (!$guide) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Dokumen panduan tidak ditemukan.'], 404);
            }
            abort(404, 'Dokumen panduan tidak ditemukan');
        }

        $filePath = null;

        if (!empty($guide->file_path)) {
            $cleanRel = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $guide->file_path), DIRECTORY_SEPARATOR);
            $candidates = [
                $guide->file_path, // If already an absolute path
                storage_path('app' . DIRECTORY_SEPARATOR . $cleanRel),
                storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . $cleanRel),
                storage_path($cleanRel),
                public_path($cleanRel),
                storage_path('app' . DIRECTORY_SEPARATOR . 'guidebooks' . DIRECTORY_SEPARATOR . basename($guide->file_path)),
                storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'guidebooks' . DIRECTORY_SEPARATOR . basename($guide->file_path)),
            ];

            foreach ($candidates as $cand) {
                if (file_exists($cand) && is_file($cand) && filesize($cand) > 0) {
                    $filePath = $cand;
                    break;
                }
            }
        }

        // If file does not exist on disk or empty, auto-generate official PDF
        if (!$filePath || !file_exists($filePath)) {
            $filePath = SimplePdfBuilder::generateForGuide($guide);
            $relPath = 'guidebooks/' . basename($filePath);
            $guide->update(['file_path' => $relPath]);
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="protected_' . $guide->id . '.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    }

    public function resetDefaults()
    {
        GuideBook::ensureTableExists();
        return redirect('/?tab=guides')
            ->with('info', 'Data dummy dinonaktifkan.');
    }
}
