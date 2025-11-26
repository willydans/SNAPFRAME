<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image; // Pastikan library ini terinstall

class SnapController extends Controller
{
    /**
     * 1. DASHBOARD: Halaman Statistik & Ringkasan
     * Route: /dashboard
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Hitung total projek user untuk ditampilkan di widget statistik dashboard
        $totalProjek = Job::where('user_id', $userId)->count();
        
        // Kita kirim variable $totalProjek ke view 'dashboard'
        return view('dashboard', compact('totalProjek'));
    }

    /**
     * 2. PEKERJAAN SAYA: Halaman History Lengkap
     * Route: /pekerjaan-saya
     */
    public function pekerjaan()
    {
        $userId = Auth::id();
        
        // Ambil SEMUA data pekerjaan milik user, urutkan dari terbaru
        // Include 'frame' biar kita bisa ambil nama frame-nya
        $jobs = Job::with('frame')->where('user_id', $userId)->latest()->get();
        
        // Kirim data $jobs ke view 'pekerjaan' (file pekerjaan.blade.php)
        return view('pekerjaan', compact('jobs'));
    }

    /**
     * 3. GALERI FRAME: Katalog Pilihan Frame
     * Route: /frames
     */
    public function gallery()
    {
        $frames = Frame::all();
        
        // Mengembalikan view khusus galeri. 
        // Pastikan kamu punya file 'resources/views/frames/index.blade.php' 
        // atau ubah jadi 'welcome' jika galeri ada di landing page.
        return view('frames.index', compact('frames'));
    }

    /**
     * 4. PROFIL: Halaman Profil User
     * Route: /profile
     */
    public function profile()
    {
        $userId = Auth::id();
        
        // Hitung jumlah job untuk statistik di halaman profil
        $jobs_count = Job::where('user_id', $userId)->count();

        return view('profile', compact('jobs_count'));
    }

    /**
     * 5. CREATE: Form Upload
     * Route: /upload (GET)
     */
    public function create(Request $request)
    {
        $frames = Frame::all();
        
        // Menangkap frame_id jika user memilih dari galeri
        $selectedFrameId = $request->query('frame_id'); 
        
        return view('upload', compact('frames', 'selectedFrameId'));
    }

    /**
     * 6. STORE: Proses Logic Berat (Upload & Merge)
     * Route: /upload (POST)
     */
    public function store(Request $request)
    {
        // --- 1. Validasi Dasar ---
        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required',
            'frame_id' => 'required|exists:frames,id',
            'photos' => 'required|array', 
            'photos.*' => 'image|max:5120', // Tiap foto max 5MB
        ]);

        $user = Auth::user();
        $frame = Frame::find($request->frame_id);
        $uploadedPhotos = $request->file('photos');

        // --- Logic: Validasi Jumlah Foto ---
        if (count($uploadedPhotos) !== $frame->max_photos) {
            return back()
                ->withErrors(['msg' => "Gagal! Frame '{$frame->name}' mewajibkan tepat {$frame->max_photos} foto. Anda mengupload " . count($uploadedPhotos) . " foto."])
                ->withInput();
        }

        try {
            // --- Logic: Persiapan Canvas ---
            $canvas = Image::make($frame->image_url);
            $coordinates = json_decode($frame->coordinates, true);

            if (empty($coordinates)) {
                throw new \Exception("Konfigurasi koordinat frame belum diset oleh Admin.");
            }

            // --- Logic: Looping & Tempel Foto ---
            foreach ($uploadedPhotos as $index => $photoFile) {
                if (!isset($coordinates[$index])) break;
                
                $slot = $coordinates[$index]; 
                $imgUser = Image::make($photoFile);
                
                // Resize & Crop agar pas di lubang frame
                $imgUser->fit($slot['w'], $slot['h']);

                // Tempelkan ke canvas
                $canvas->insert($imgUser, 'top-left', $slot['x'], $slot['y']);
            }

            // --- Logic: Upload ke Cloud (OCI) ---
            $filename = 'COLLAGE_' . time() . '_' . Str::random(10) . '.jpg';
            $resultStream = $canvas->stream('jpg', 90);
            
            Storage::disk('oci')->put('results/' . $filename, $resultStream);

            // URL Hasil
            $ociBaseUrl = rtrim(config('filesystems.disks.oci.url'), '/');
            $bucketName = config('filesystems.disks.oci.bucket');
            $finalResultUrl = "{$ociBaseUrl}/{$bucketName}/results/{$filename}";

            // --- Logic: Simpan ke Database ---
            Job::create([
                'user_id' => $user->id,
                'frame_id' => $frame->id,
                'name' => $request->name,
                'priority' => $request->priority,
                'original_file' => 'results/' . $filename, 
                'status' => 'COMPLETE',
                'result_url' => $finalResultUrl
            ]);

            // REDIRECT KE PEKERJAAN SAYA (Bukan Dashboard lagi, biar langsung lihat hasil)
            return redirect()->route('pekerjaan')->with('success', 'Kolase berhasil dibuat! Cek hasilnya di sini.');

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Gagal memproses: ' . $e->getMessage()])->withInput();
        }
    }
}