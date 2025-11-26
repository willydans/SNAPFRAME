<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Frame;

class FrameSeeder extends Seeder
{
    public function run(): void
    {
        // URL BASE (Sesuaikan namespace 'ax28k1agvutq' dengan punya kamu)
        $baseUrl = 'https://objectstorage.ap-batam-1.oraclecloud.com/n/ax28k1agvutq/b/snapframe-assets/o/frames/';

        // -----------------------------------------------------------
        // 1. Grey Minimalist Story (11 Foto)
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Grey Minimalist Story',
            'description' => 'Kolase estetik 11 foto untuk story Instagram.',
            'image_url' => $baseUrl . 'grey-collage.png', 
            'asset_path' => 'frames/grey-collage.png',
            'max_photos' => 11, 
            'coordinates' => json_encode([
                // Baris 1
                ['x' => 50,  'y' => 50,  'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 50,  'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 50,  'w' => 300, 'h' => 400], 
                // Baris 2
                ['x' => 50,  'y' => 500, 'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 500, 'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 500, 'w' => 300, 'h' => 400], 
                // Baris 3
                ['x' => 50,  'y' => 950, 'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 950, 'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 950, 'w' => 300, 'h' => 400], 
                // Baris 4
                ['x' => 50,  'y' => 1400, 'w' => 465, 'h' => 350],
                ['x' => 565, 'y' => 1400, 'w' => 465, 'h' => 350],
            ]),
        ]);

        // -----------------------------------------------------------
        // 2. Blue Fun Photostrip (4 Foto)
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Blue Fun Photostrip',
            'description' => 'Photostrip lucu dengan tema langit dan pesawat.',
            'image_url' => $baseUrl . 'blue-photostrip.png', 
            'asset_path' => 'frames/blue-photostrip.png',
            'max_photos' => 4, 
            'coordinates' => json_encode([
                ['x' => 80,  'y' => 280,  'w' => 840, 'h' => 550], 
                ['x' => 80,  'y' => 920,  'w' => 840, 'h' => 550], 
                ['x' => 80,  'y' => 1560, 'w' => 840, 'h' => 550], 
                ['x' => 80,  'y' => 2200, 'w' => 840, 'h' => 550], 
            ]),
        ]);

        // -----------------------------------------------------------
        // 3. Brown Aesthetic Strip (3 Foto)
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Brown Aesthetic Strip',
            'description' => 'Nuansa cokelat hangat dengan kutipan motivasi.',
            'image_url' => $baseUrl . 'brown-photostrip.png', 
            'asset_path' => 'frames/brown-photostrip.png',
            'max_photos' => 3, 
            'coordinates' => json_encode([
                ['x' => 150, 'y' => 180, 'w' => 700, 'h' => 700],
                ['x' => 150, 'y' => 1030, 'w' => 700, 'h' => 700],
                ['x' => 150, 'y' => 1880, 'w' => 700, 'h' => 700],
            ]),
        ]);

        // -----------------------------------------------------------
        // 4. FRAME BARU: Pink Summer Collage (9 Foto)
        // -----------------------------------------------------------
        // Asumsi gambar: 1080x1350 (4:5 Portrait) atau sejenisnya.
        // Grid 3x3.
        Frame::create([
            'name' => 'Pink Summer Collage',
            'description' => 'Kolase musim panas ceria dengan 9 slot foto.',
            'image_url' => $baseUrl . 'pink-collage.png', 
            'asset_path' => 'frames/pink-collage.png',
            'max_photos' => 9, 
            'coordinates' => json_encode([
                // BARIS 1
                ['x' => 35,  'y' => 35,  'w' => 320, 'h' => 320], // Kiri Atas
                ['x' => 380, 'y' => 35,  'w' => 320, 'h' => 320], // Tengah Atas
                ['x' => 725, 'y' => 35,  'w' => 320, 'h' => 320], // Kanan Atas

                // BARIS 2
                ['x' => 35,  'y' => 380, 'w' => 320, 'h' => 320], // Kiri Tengah
                ['x' => 380, 'y' => 380, 'w' => 320, 'h' => 320], // Tengah Tengah
                ['x' => 725, 'y' => 380, 'w' => 320, 'h' => 320], // Kanan Tengah

                // BARIS 3
                ['x' => 35,  'y' => 725, 'w' => 320, 'h' => 320], // Kiri Bawah
                ['x' => 380, 'y' => 725, 'w' => 320, 'h' => 320], // Tengah Bawah
                ['x' => 725, 'y' => 725, 'w' => 320, 'h' => 320], // Kanan Bawah
            ]),
        ]);
    }
}