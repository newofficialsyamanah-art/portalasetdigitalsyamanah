<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mikrotik_accounts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama_pemilik')->nullable()->index();
            $table->string('tim')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('username')->index();
            $table->string('password')->nullable();
            $table->string('profile_hotspot')->nullable();
            $table->string('rate_limit')->nullable();
            $table->text('command_terminal')->nullable();
            $table->string('status')->default('aktif');
            $table->string('catatan')->nullable();
            $table->string('created_at_str')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrotik_accounts');
    }
};
