<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class SnapController extends Controller
{
    public function gallery()
    {
        $frames = Frame::all();
        return view('frames.index', compact('frames'));
    }

    public function index()
    {
        $userId = Auth::id();
        $jobs = Job::with('frame')->where('user_id', $userId)->latest()->get();
        return view('dashboard', compact('jobs'));
    }

    public function create(Request $request)
    {
        $frames = Frame::all();
        $selectedFrameId = $request->query('frame_id'); 
        return view('upload', compact('frames', 'selectedFrameId'));
    }

    /**
     * PROSES UTAMA: Multi-Photo Logic
     */
    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required',
            'frame_id' => 'required|exists:frames,id',
            // Ubah validasi menjadi array untuk menangani banyak foto
            'photos' => 'required|array', 
            'photos.*' => 'image|max:5120', // Tiap foto max 5MB
        ]);

        $user = Auth::user();
        $frame = Frame::find($request->frame_id);
        $uploadedPhotos = $request->file('photos');

        // ---------------------------------------------------------
        // LOGIC 1: VALIDASI JUMLAH FOTO
        // ---------------------------------------------------------
        // Cek apakah jumlah yang diupload SAMA PERSIS dengan kapasitas frame
        if (count($uploadedPhotos) !== $frame->max_photos) {
            return back()
                ->withErrors(['msg' => "Gagal! Frame '{$frame->name}' mewajibkan tepat {$frame->max_photos} foto. Anda mengupload " . count($uploadedPhotos) . " foto."])
                ->withInput();
        }

        try {
            // ---------------------------------------------------------
            // LOGIC 2: PERSIAPAN CANVAS (FRAME)
            // ---------------------------------------------------------
            
            // Download Frame dari OCI sebagai background dasar
            // Canvas ini ukurannya besar (misal 1080x1920)
            $canvas = Image::make($frame->image_url);
            
            // Ambil Peta Koordinat dari Database
            // Format JSON: [{"x":50,"y":50,"w":300,"h":400}, ...]
            $coordinates = json_decode($frame->coordinates, true);

            // Jika koordinat kosong/salah set di seeder, fallback ke mode single center
            if (empty($coordinates)) {
                throw new \Exception("Konfigurasi koordinat frame belum diset oleh Admin.");
            }

            // ---------------------------------------------------------
            // LOGIC 3: LOOPING & TEMPEL FOTO
            // ---------------------------------------------------------

            foreach ($uploadedPhotos as $index => $photoFile) {
                // Ambil data koordinat untuk urutan foto ke-$index
                if (!isset($coordinates[$index])) break; // Jaga-jaga index overflow
                
                $slot = $coordinates[$index]; 

                // 1. Baca Foto User
                $imgUser = Image::make($photoFile);

                // 2. Resize/Crop Foto User agar PAS dengan ukuran Lubang (w, h)
                // Fungsi 'fit' akan otomatis crop tengah jika rasio beda
                $imgUser->fit($slot['w'], $slot['h']);

                // 3. Tempelkan Foto User ke Canvas Frame
                // Gunakan posisi X dan Y dari database
                // 'top-left' berarti titik acuan koordinat adalah pojok kiri atas foto
                $canvas->insert($imgUser, 'top-left', $slot['x'], $slot['y']);
            }

            // ---------------------------------------------------------
            // LOGIC 4: UPLOAD HASIL KE CLOUD
            // ---------------------------------------------------------

            $filename = 'COLLAGE_' . time() . '_' . Str::random(10) . '.jpg';
            $resultStream = $canvas->stream('jpg', 90); // Render jadi JPG High Quality
            
            Storage::disk('oci')->put('results/' . $filename, $resultStream);

            // Konstruksi URL Hasil
            $ociBaseUrl = rtrim(config('filesystems.disks.oci.url'), '/');
            $bucketName = config('filesystems.disks.oci.bucket');
            $finalResultUrl = "{$ociBaseUrl}/{$bucketName}/results/{$filename}";

            // ---------------------------------------------------------
            // LOGIC 5: SIMPAN KE DATABASE
            // ---------------------------------------------------------
            
            Job::create([
                'user_id' => $user->id,
                'frame_id' => $frame->id,
                'name' => $request->name,
                'priority' => $request->priority,
                // Kita simpan path hasil saja karena original filenya banyak
                'original_file' => 'results/' . $filename, 
                'status' => 'COMPLETE',
                'result_url' => $finalResultUrl
            ]);

            return redirect()->route('dashboard')->with('success', 'Kolase berhasil dibuat! Cek hasilnya.');

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal memproses: ' . $e->getMessage()])->withInput();
        }
    }
}