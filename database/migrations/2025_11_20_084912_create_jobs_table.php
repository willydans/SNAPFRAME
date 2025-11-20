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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            
            // User ID (Kita set default 1/Dummy agar tidak error jika belum implementasi Login penuh)
            $table->unsignedBigInteger('user_id')->default(1); 
            
            // Relasi ke tabel frames
            $table->foreignId('frame_id')->constrained()->onDelete('cascade');
            
            // Data Input User (Sesuai UI)
            $table->string('name')->nullable(); // Nama Pekerjaan (misal: Liburan Keluarga Bali)
            $table->string('priority')->default('Normal'); // Prioritas (Normal/Tinggi)
            
            // Data File Cloud
            $table->string('original_file'); // Path file mentah di OCI Bucket Input
            $table->string('result_url')->nullable(); // URL file hasil di OCI Bucket Output (Diisi setelah proses selesai)
            
            // Status Proses (PENDING -> PROCESSING -> COMPLETE)
            $table->string('status')->default('PENDING');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};