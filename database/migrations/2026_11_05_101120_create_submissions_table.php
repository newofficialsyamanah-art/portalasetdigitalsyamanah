<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('type')->index(); // 'pc' or 'akun'
            $table->string('timestamp')->nullable();
            $table->string('nama')->nullable()->index();
            $table->string('departemen')->nullable()->index();
            $table->string('jabatan')->nullable();
            $table->string('jenis_perangkat')->nullable();
            $table->json('data');
            $table->boolean('synced_to_google')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
