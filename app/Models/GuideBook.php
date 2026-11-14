<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class GuideBook extends Model
{
    protected $fillable = [
        'target_role',
        'target_division',
        'system_name',
        'feature_name',
        'title',
        'category',
        'version',
        'author',
        'summary',
        'content',
        'document_url',
        'file_path',
        'tags',
        'is_pinned',
        'views_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'views_count' => 'integer',
    ];

    /**
     * Auto-ensure table exists and update columns safely (No dummy data)
     */
    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('guide_books')) {
            Schema::create('guide_books', function (Blueprint $table) {
                $table->id();
                $table->string('target_division')->default('Semua Divisi')->nullable();
                $table->string('target_role')->default('Semua Karyawan (Umum)')->nullable();
                $table->string('system_name');
                $table->string('feature_name')->nullable();
                $table->string('title');
                $table->string('category')->default('Hardware');
                $table->string('version')->default('v1.0');
                $table->string('author')->default('Tim IT Syamanah');
                $table->text('summary')->nullable();
                $table->longText('content')->nullable();
                $table->string('document_url')->nullable();
                $table->string('file_path')->nullable();
                $table->text('tags')->nullable();
                $table->boolean('is_pinned')->default(false);
                $table->integer('views_count')->default(0);
                $table->timestamps();
            });
        } else {
            // Check for missing columns and add them safely
            if (!Schema::hasColumn('guide_books', 'target_division')) {
                Schema::table('guide_books', function (Blueprint $table) {
                    $table->string('target_division')->default('Semua Divisi')->nullable();
                });
            }
            if (!Schema::hasColumn('guide_books', 'target_role')) {
                Schema::table('guide_books', function (Blueprint $table) {
                    $table->string('target_role')->default('Semua Karyawan (Umum)')->nullable();
                });
            }
            if (!Schema::hasColumn('guide_books', 'feature_name')) {
                Schema::table('guide_books', function (Blueprint $table) {
                    $table->string('feature_name')->nullable();
                });
            }
        }

        // Delete all dummy SOP records permanently
        $dummyTitles = [
            'SOP Ekspor Spesifikasi DxDiag & Pengunggahan Bukti Google Drive',
            'Panduan Integrasi Frappe REST API & Sinkronisasi Asset Management',
            'Panduan Operasional Generator Hotspot & Voucher MikroTik Syamanah',
            'SOP Inventarisasi Handphone Sales & Pengamanan Kredensial Akun',
            'Standar Operasional Penomoran IP Statis & Subnetting LAN Kantor',
            'SOP Integrasi Google Apps Script (GAS) & Sinkronisasi Webhook 1:1',
            'Buku Panduan Tata Kelola Matriks Hak Akses & Keamanan Sistem Portal',
        ];

        $dummyRecords = static::whereIn('title', $dummyTitles)->get();
        foreach ($dummyRecords as $dummy) {
            if ($dummy->file_path) {
                if (file_exists(storage_path('app/' . $dummy->file_path))) {
                    @unlink(storage_path('app/' . $dummy->file_path));
                }
                if (file_exists(public_path($dummy->file_path))) {
                    @unlink(public_path($dummy->file_path));
                }
            }
            $dummy->delete();
        }

        $dummySystems = [
            'Spesifikasi PC & DxDiag',
            'ERPNext Frappe REST API',
            'MikroTik RouterOS & Hotspot',
            'Smartphone & Akun Sales',
            'Jaringan Kantor & Pemetaan IP',
            'Google Spreadsheet & Apps Script',
            'Portal EAM & Hak Akses RBAC',
        ];
        static::whereIn('system_name', $dummySystems)->delete();
    }
}
