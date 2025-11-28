<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Frame;

class FrameSeeder extends Seeder
{
    public function run(): void
    {
        // GANTI INI DENGAN URL BUCKET KAMU YANG BENAR
        $baseUrl = 'https://objectstorage.ap-batam-1.oraclecloud.com/n/ax28k1agvutq/b/snapframe-assets/o/frames/';

        // -----------------------------------------------------------
        // 1. Grey Minimalist Story (11 Foto) - SUDAH OKE (GRID)
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Grey Minimalist Story',
            'description' => 'Kolase estetik 11 foto untuk story Instagram.',
            'image_url' => $baseUrl . 'grey-collage.png', 
            'asset_path' => 'frames/grey-collage.png',
            'max_photos' => 11, 
            'coordinates' => json_encode([
                ['x' => 50,  'y' => 50,  'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 50,  'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 50,  'w' => 300, 'h' => 400], 
                ['x' => 50,  'y' => 500, 'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 500, 'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 500, 'w' => 300, 'h' => 400], 
                ['x' => 50,  'y' => 950, 'w' => 300, 'h' => 400], 
                ['x' => 390, 'y' => 950, 'w' => 300, 'h' => 400], 
                ['x' => 730, 'y' => 950, 'w' => 300, 'h' => 400], 
                ['x' => 50,  'y' => 1400, 'w' => 465, 'h' => 350],
                ['x' => 565, 'y' => 1400, 'w' => 465, 'h' => 350],
            ]),
        ]);

        // -----------------------------------------------------------
        // 2. Blue Fun Photostrip (4 Foto) - DI-REVISI (AGAR MUAT)
        // -----------------------------------------------------------
        // Asumsi Tinggi Frame 1920px. Slot dikecilkan biar muat 4 biji.
        Frame::create([
            'name' => 'Blue Fun Photostrip',
            'description' => 'Photostrip lucu dengan tema langit dan pesawat.',
            'image_url' => $baseUrl . 'blue-photostrip.png', 
            'asset_path' => 'frames/blue-photostrip.png',
            'max_photos' => 4, 
            'coordinates' => json_encode([
                // Tinggi slot dikurangi jadi 360 (sebelumnya 550) agar muat vertikal
                // Jarak antar foto sekitar 40-50px
                ['x' => 120, 'y' => 180,  'w' => 840, 'h' => 360], 
                ['x' => 120, 'y' => 580,  'w' => 840, 'h' => 360], 
                ['x' => 120, 'y' => 980,  'w' => 840, 'h' => 360], 
                ['x' => 120, 'y' => 1380, 'w' => 840, 'h' => 360], 
            ]),
        ]);

        // -----------------------------------------------------------
        // 3. Brown Aesthetic Strip (3 Foto) - DI-REVISI (AGAR MUAT)
        // -----------------------------------------------------------
        // Slot sebelumnya Y=1880 itu DI LUAR layar (karena tinggi HD cuma 1920).
        Frame::create([
            'name' => 'Brown Aesthetic Strip',
            'description' => 'Nuansa cokelat hangat dengan kutipan motivasi.',
            'image_url' => $baseUrl . 'brown-photostrip.png', 
            'asset_path' => 'frames/brown-photostrip.png',
            'max_photos' => 3, 
            'coordinates' => json_encode([
                // Kita geser ke atas dan kecilkan sedikit tingginya jadi 480
                // Agar 3 foto muat dengan rapi di tengah
                ['x' => 150, 'y' => 250,  'w' => 700, 'h' => 480],
                ['x' => 150, 'y' => 780,  'w' => 700, 'h' => 480],
                ['x' => 150, 'y' => 1310, 'w' => 700, 'h' => 480],
            ]),
        ]);

        // -----------------------------------------------------------
        // 4. Pink Summer Collage (9 Foto) - SUDAH OKE (GRID)
        // -----------------------------------------------------------
        Frame::create([
            'name' => 'Pink Summer Collage',
            'description' => 'Kolase musim panas ceria dengan 9 slot foto.',
            'image_url' => $baseUrl . 'pink-collage.png', 
            'asset_path' => 'frames/pink-collage.png',
            'max_photos' => 9, 
            'coordinates' => json_encode([
                ['x' => 35,  'y' => 35,  'w' => 320, 'h' => 320], 
                ['x' => 380, 'y' => 35,  'w' => 320, 'h' => 320], 
                ['x' => 725, 'y' => 35,  'w' => 320, 'h' => 320], 

                ['x' => 35,  'y' => 380, 'w' => 320, 'h' => 320], 
                ['x' => 380, 'y' => 380, 'w' => 320, 'h' => 320], 
                ['x' => 725, 'y' => 380, 'w' => 320, 'h' => 320], 

                ['x' => 35,  'y' => 725, 'w' => 320, 'h' => 320], 
                ['x' => 380, 'y' => 725, 'w' => 320, 'h' => 320], 
                ['x' => 725, 'y' => 725, 'w' => 320, 'h' => 320], 
            ]),
        ]);
    }
}