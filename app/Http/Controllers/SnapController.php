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
    /**
     * Halaman Utama (Galeri Frame)
     * Menampilkan semua pilihan frame yang tersedia di database.
     */
    public function gallery()
    {
        $frames = Frame::all();
        return view('frames.index', compact('frames'));
    }

    /**
     * Halaman Dashboard (Pekerjaan Saya)
     * Menampilkan riwayat pekerjaan milik user yang sedang login.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Ambil data job, urutkan dari yang terbaru
        $jobs = Job::with('frame')
                    ->where('user_id', $userId) 
                    ->latest()
                    ->get();

        return view('dashboard', compact('jobs'));
    }

    /**
     * Halaman Form Upload
     * Menerima parameter 'frame_id' jika user memilih dari galeri.
     */
    public function create(Request $request)
    {
        $frames = Frame::all();
        
        // Tangkap frame_id dari URL (misal: /upload?frame_id=1)
        $selectedFrameId = $request->query('frame_id'); 

        return view('upload', compact('frames', 'selectedFrameId'));
    }

    /**
     * PROSES UTAMA: Validasi -> Edit Gambar -> Upload Cloud -> Simpan DB
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required',
            'frame_id' => 'required|exists:frames,id',
            'photo' => 'required|image|max:5120', // Maksimal 5MB
        ]);

        // Ambil data user & frame
        $user = Auth::user();
        $frame = Frame::find($request->frame_id);
        $userPhotoFile = $request->file('photo');

        try {
            // --- A. PENGOLAHAN GAMBAR (Intervention Image) ---

            // 1. Siapkan Foto User (Resize jadi kotak 1080x1080)
            $imgUser = Image::make($userPhotoFile);
            $imgUser->fit(1080, 1080); 

            // 2. Siapkan Frame (Download dari URL OCI)
            // Pastikan URL frame di database bisa diakses publik
            $imgFrame = Image::make($frame->image_url);
            $imgFrame->resize(1080, 1080);

            // 3. Gabungkan (Tempel Frame di atas Foto User)
            $imgUser->insert($imgFrame, 'center');


            // --- B. UPLOAD KE ORACLE CLOUD (OCI) ---

            // 1. Buat nama file unik
            $filename = 'RESULT_' . time() . '_' . Str::random(10) . '.jpg';
            
            // 2. Ubah gambar jadi stream data (Format JPG, Kualitas 90%)
            $resultStream = $imgUser->stream('jpg', 90);

            // 3. Upload stream tersebut ke Bucket OCI (Folder 'results')
            // Menggunakan disk 'oci' yang sudah disetup di config/filesystems.php
            Storage::disk('oci')->put('results/' . $filename, $resultStream);


            // --- C. KONSTRUKSI URL HASIL ---
            
            // Ambil Base URL dan Nama Bucket dari Config
            $ociBaseUrl = config('filesystems.disks.oci.url'); 
            $bucketName = config('filesystems.disks.oci.bucket');
            
            // Bersihkan slash di ujung URL jika ada
            $ociBaseUrl = rtrim($ociBaseUrl, '/');
            
            // Gabungkan menjadi URL lengkap: {endpoint}/{bucket}/{folder}/{filename}
            // Catatan: Sesuaikan struktur URL ini dengan OCI Anda jika berbeda
            $finalResultUrl = "{$ociBaseUrl}/{$bucketName}/results/{$filename}";


            // --- D. SIMPAN KE DATABASE ---

            Job::create([
                'user_id' => $user->id,
                'frame_id' => $frame->id,
                'name' => $request->name,
                'priority' => $request->priority,
                'original_file' => 'results/' . $filename,
                'status' => 'COMPLETE', // Langsung selesai karena diproses saat ini juga
                'result_url' => $finalResultUrl
            ]);

            // Redirect sukses
            return redirect()->route('dashboard')->with('success', 'Foto berhasil diproses dan disimpan di Cloud!');

        } catch (\Exception $e) {
            // Jika error (misal koneksi OCI putus atau library error)
            // Log error untuk debugging developer
            \Illuminate\Support\Facades\Log::error("Gagal memproses gambar: " . $e->getMessage());
            
            return back()->withErrors(['msg' => 'Gagal memproses gambar. Pastikan koneksi internet stabil. Error: ' . $e->getMessage()])->withInput();
        }
    }
}