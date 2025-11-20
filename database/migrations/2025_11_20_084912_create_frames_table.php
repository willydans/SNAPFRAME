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
        Schema::create('frames', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama bingkai (misal: Grid Emas 6 Foto)
            $table->text('description')->nullable(); // Deskripsi (misal: Layout 3x2...)
            $table->string('image_url'); // URL gambar preview (untuk ditampilkan di Galeri Web)
            $table->string('asset_path')->nullable(); // Path file .png bingkai asli di OCI (untuk dipakai Function memproses gambar)
            $table->integer('max_photos')->default(1); // Jumlah foto yang muat (opsional, default 1)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frames');
    }
};