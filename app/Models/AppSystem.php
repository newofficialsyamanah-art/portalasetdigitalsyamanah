<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class AppSystem extends Model
{
    protected $table = 'app_systems';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'category',
        'description',
        'features',
        'order_num',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'order_num' => 'integer',
    ];

    /**
     * Ensure table exists safely with NO automatic dummy data
     */
    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('app_systems')) {
            Schema::create('app_systems', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->string('icon')->default('💻');
                $table->string('category')->default('SOP Operasional');
                $table->text('description')->nullable();
                $table->json('features')->nullable();
                $table->integer('order_num')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Purge any previously auto-seeded dummy systems permanently
        $dummySlugs = [
            'spesifikasi-pc-dxdiag',
            'erpnext-frappe-rest-api',
            'mikrotik-routeros-hotspot',
            'smartphone-akun-sales',
            'jaringan-kantor-pemetaan-ip',
            'google-spreadsheet-apps-script',
            'portal-eam-rbac',
        ];
        try {
            static::whereIn('slug', $dummySlugs)->delete();
        } catch (\Throwable $e) {}
    }

    /**
     * Dummy seeding permanently disabled by user request
     */
    public static function seedDefaults(): void
    {
        return;
    }

    /**
     * Guide books relationship
     */
    public function guideBooks()
    {
        return $this->hasMany(GuideBook::class, 'system_name', 'name');
    }
}
