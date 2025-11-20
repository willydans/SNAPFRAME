<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Frame;

class FrameSeeder extends Seeder
{
    public function run(): void
    {
        // URL Dasar Bucket OCI (Ganti dengan milik Anda jika berbeda)
        $baseUrl = 'https://objectstorage.ap-batam-1.oraclecloud.com/n/ax28k1agvutq/b/snapframe-assets/o/frames/';

        // -----------------------------------------------------------
        // 1. FRAME BARU: Grey Minimalist Story (Sesuai Gambar Upload)
        // -----------------------------------------------------------
        // Analisis Gambar: 
        // - Ukuran Kanvas: Asumsi 1080x1920 (Story)
        // - Total Slot: 11 Foto
        // - Baris 1-3: Grid 3 kolom
        // - Baris 4: Grid 2 kolom (Landscape)
        Frame::create([
            'name' => 'Grey Minimalist Story',
            'description' => 'Kolase estetik 11 foto untuk story Instagram.',
            'image_url' => $baseUrl . 'grey-collage.png', 
            'asset_path' => 'frames/grey-collage.png',
            'max_photos' => 11, // WAJIB upload 11 foto
            'coordinates' => json_encode([
                // --- BARIS 1 (3 Foto Portrait) ---
                // x, y = posisi titik kiri atas | w, h = lebar & tinggi foto
                ['x' => 50,  'y' => 50,  'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 50,  'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 50,  'w' => 300, 'h' => 400], 

                // --- BARIS 2 (3 Foto Portrait) ---
                ['x' => 50,  'y' => 500, 'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 500, 'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 500, 'w' => 300, 'h' => 400], 

                // --- BARIS 3 (3 Foto Portrait) ---
                ['x' => 50,  'y' => 950, 'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 950, 'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 950, 'w' => 300, 'h' => 400], 

                // --- BARIS 4 (2 Foto Landscape Lebar) ---
                ['x' => 50,  'y' => 1400, 'w' => 465, 'h' => 350], // Kiri Bawah
                ['x' => 565, 'y' => 1400, 'w' => 465, 'h' => 350], // Kanan Bawah
            ]),
        ]);

        // -----------------------------------------------------------
        // 2. Grid Emas (6 Foto) - Update dengan Koordinat
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Grid Emas 6 Foto',
            'description' => 'Tampilan mewah dengan aksen emas.',
            'image_url' => $baseUrl . 'gold-frame.png', 
            'asset_path' => 'frames/gold-frame.png', 
            'max_photos' => 6,
            'coordinates' => json_encode([
                // Baris 1 (3 Foto) - Asumsi layout landscape 3x2
                ['x' => 50,  'y' => 100, 'w' => 300, 'h' => 300],
                ['x' => 390, 'y' => 100, 'w' => 300, 'h' => 300],
                ['x' => 730, 'y' => 100, 'w' => 300, 'h' => 300],
                // Baris 2 (3 Foto)
                ['x' => 50,  'y' => 450, 'w' => 300, 'h' => 300],
                ['x' => 390, 'y' => 450, 'w' => 300, 'h' => 300],
                ['x' => 730, 'y' => 450, 'w' => 300, 'h' => 300],
            ]),
        ]);

        // -----------------------------------------------------------
        // 3. Polaroid Klasik (1 Foto)
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Polaroid Klasik',
            'description' => 'Nuansa vintage sederhana.',
            'image_url' => $baseUrl . 'polaroid.png',
            'asset_path' => 'frames/polaroid.png',
            'max_photos' => 1,
            'coordinates' => json_encode([
                // Hanya 1 slot di tengah agak atas
                ['x' => 100, 'y' => 100, 'w' => 880, 'h' => 880]
            ]),
        ]);

        // -----------------------------------------------------------
        // 4. Modern Hitam (1 Foto)
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Modern Hitam Minimalis',
            'description' => 'Desain bersih dan tegas.',
            'image_url' => $baseUrl . 'modern-black.png',
            'asset_path' => 'frames/modern-black.png',
            'max_photos' => 1,
            'coordinates' => json_encode([
                // 1 Slot Full
                ['x' => 50, 'y' => 50, 'w' => 980, 'h' => 980]
            ]),
        ]);
    }
}