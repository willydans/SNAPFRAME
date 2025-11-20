<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Frame;

class FrameSeeder extends Seeder
{
    public function run(): void
    {
        // URL Dasar Bucket Public OCI Anda (Sesuaikan dengan namespace & region Anda)
        // Contoh format: https://{namespace}.objectstorage.{region}.oraclecloud.com/n/{namespace}/b/{bucket}/o/
        $baseUrl = 'https://m317051054.objectstorage.ap-batam-1.oraclecloud.com/n/m317051054/b/snapframe-assets/o/frames/';

        Frame::create([
            'name' => 'Grid Emas 6 Foto',
            'description' => 'Tampilan mewah dengan aksen emas untuk momen spesial.',
            // URL ini dipakai Website untuk menampilkan gambar di halaman Galeri
            'image_url' => $baseUrl . 'gold-frame.png', 
            // Path ini dipakai OCI Function nanti untuk mengambil file saat proses merging
            'asset_path' => 'frames/gold-frame.png', 
            'max_photos' => 6
        ]);

        Frame::create([
            'name' => 'Polaroid Klasik',
            'description' => 'Nuansa vintage sederhana untuk kenangan manis.',
            'image_url' => $baseUrl . 'polaroid.png',
            'asset_path' => 'frames/polaroid.png',
            'max_photos' => 1
        ]);

        Frame::create([
            'name' => 'Modern Hitam Minimalis',
            'description' => 'Desain bersih dan tegas untuk foto profesional.',
            'image_url' => $baseUrl . 'modern-black.png',
            'asset_path' => 'frames/modern-black.png',
            'max_photos' => 1
        ]);
    }
}