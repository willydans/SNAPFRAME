<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('frames', function (Blueprint $table) {
            // Menambahkan kolom JSON 'coordinates' setelah 'max_photos'
            // Kolom ini akan menyimpan array koordinat [{x, y, w, h}, ...]
            $table->json('coordinates')->nullable()->after('max_photos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frames', function (Blueprint $table) {
            $table->dropColumn('coordinates');
        });
    }
};