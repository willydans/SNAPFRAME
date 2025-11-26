<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class SnapController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $totalProjek = Job::where('user_id', $userId)->count();
        $recentJobs = Job::with('frame')->where('user_id', $userId)->latest()->take(5)->get();
        return view('dashboard', compact('totalProjek', 'recentJobs'));
    }

    public function pekerjaan()
    {
        $userId = Auth::id();
        $jobs = Job::with('frame')->where('user_id', $userId)->latest()->get();
        return view('pekerjaan', compact('jobs'));
    }

    public function gallery()
    {
        $frames = Frame::all();
        return view('frames.index', compact('frames'));
    }

    public function profile()
    {
        $userId = Auth::id();
        $jobs_count = Job::where('user_id', $userId)->count();
        return view('profile', compact('jobs_count'));
    }

    public function create(Request $request)
    {
        $frames = Frame::all();
        $selectedFrameId = $request->query('frame_id'); 
        return view('gallery.upload', compact('frames', 'selectedFrameId'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input User
        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required',
            'frame_id' => 'required|exists:frames,id',
            'photos' => 'required|array', 
            'photos.*' => 'image|max:5120', // Max 5MB
        ]);

        $user = Auth::user();
        $frame = Frame::find($request->frame_id);
        $uploadedPhotos = $request->file('photos');

        if (count($uploadedPhotos) !== $frame->max_photos) {
            return back()
                ->withErrors(['msg' => "Gagal! Frame '{$frame->name}' butuh {$frame->max_photos} foto."])
                ->withInput();
        }

        try {
            // --- KONFIGURASI OCI (Clean Config) ---
            $ociKey = trim(env('OCI_ACCESS_KEY_ID', ''), " \"'");
            $ociSecret = trim(env('OCI_SECRET_ACCESS_KEY', ''), " \"'");
            $ociRegion = trim(env('OCI_DEFAULT_REGION', 'ap-batam-1'), " \"'");
            $ociBucket = trim(env('OCI_BUCKET_INPUT', ''), " \"'");
            $ociEndpoint = trim(env('OCI_URL', ''), " \"'");

            if (empty($ociKey) || empty($ociSecret) || empty($ociBucket)) {
                throw new \Exception("Konfigurasi ENV OCI belum lengkap.");
            }

            // Reset Config Disk
            config([
                'filesystems.disks.oci' => [
                    'driver' => 's3',
                    'key' => $ociKey,
                    'secret' => $ociSecret,
                    'region' => $ociRegion,
                    'bucket' => $ociBucket,
                    'endpoint' => $ociEndpoint,
                    'use_path_style_endpoint' => true,
                    'throw' => true,
                ]
            ]);
            Storage::forgetDisk('oci');

            // --- STEP 1: UPLOAD RAW FILE (Backup) ---
            foreach ($uploadedPhotos as $index => $photoFile) {
                $rawFileName = 'raw_' . time() . '_' . $index . '_' . Str::random(5) . '.' . $photoFile->getClientOriginalExtension();
                Storage::disk('oci')->putFileAs('uploads', $photoFile, $rawFileName);
            }

            // --- STEP 2: PERSIAPAN LOGIC SANDWICH ---
            
            // A. Load Frame Asli (Ini akan jadi Layer Paling Atas / Rotinya)
            try {
                $frameImg = Image::make($frame->image_url);
            } catch (\Exception $imgErr) {
                throw new \Exception("Gagal download Frame: " . $frame->image_url);
            }

            // B. Buat Kanvas Kosong (Layer Paling Bawah / Piringnya)
            // Ukurannya sama persis dengan frame, warnanya putih
            $finalCanvas = Image::canvas($frameImg->width(), $frameImg->height(), '#ffffff');

            $coordinates = json_decode($frame->coordinates, true);
            if (empty($coordinates)) {
                // Fallback kalau koordinat kosong
                $coordinates = [['x' => 0, 'y' => 0, 'w' => $frameImg->width(), 'h' => $frameImg->height()]];
            }

            // --- STEP 3: PROSES MERGE FOTO (Isian Sandwich) ---
            foreach ($uploadedPhotos as $index => $photoFile) {
                if (isset($coordinates[$index])) {
                    $slot = $coordinates[$index]; 
                    $imgUser = Image::make($photoFile);
                    
                    // --- PERBAIKAN UTAMA: FORCE STRETCH & EXTRA BLEED ---
                    
                    // 1. Tambah Bleed (Lebihan) Ekstrem
                    // Kita lebihkan 50px (sebelumnya 20px) agar benar-benar menutupi lubang walau geser dikit
                    $extraBleed = 50; 
                    
                    $w_target = $slot['w'] + $extraBleed;
                    $h_target = $slot['h'] + $extraBleed;
                    
                    // 2. Ganti fit() jadi resize()
                    // 'resize' akan memaksa gambar ditarik (stretch) sesuai ukuran target
                    // Ini menjamin seluruh area tertutup pixel gambar, tidak ada cropping.
                    $imgUser->resize($w_target, $h_target);

                    // 3. Center Adjustment
                    // Geser koordinat mundur setengah dari extraBleed agar posisi tetap di tengah
                    $x_adjusted = $slot['x'] - ($extraBleed / 2);
                    $y_adjusted = $slot['y'] - ($extraBleed / 2);

                    $finalCanvas->insert($imgUser, 'top-left', $x_adjusted, $y_adjusted);
                }
            }

            // --- STEP 4: TUTUP DENGAN FRAME (Roti Atas) ---
            // Tempel frame PNG (yang bolong transparan) di atas tumpukan foto tadi
            // Pinggiran foto yang "tumpah" akan tertutup rapi oleh bingkai frame
            $finalCanvas->insert($frameImg, 'top-left', 0, 0);

            // --- STEP 5: UPLOAD HASIL ---
            $filename = 'COLLAGE_' . time() . '_' . Str::random(10) . '.jpg';
            $resultStream = $finalCanvas->stream('jpg', 90);
            
            Storage::disk('oci')->put('results/' . $filename, $resultStream);
            
            // Generate URL
            $ociBaseUrl = rtrim($ociEndpoint, '/');
            $finalResultUrl = "{$ociBaseUrl}/{$ociBucket}/results/{$filename}";

            Job::create([
                'user_id' => $user->id,
                'frame_id' => $frame->id,
                'name' => $request->name,
                'priority' => $request->priority,
                'original_file' => 'results/' . $filename, 
                'status' => 'COMPLETE',
                'result_url' => $finalResultUrl
            ]);

            return redirect()->route('dashboard')->with('success', 'Berhasil! Foto rapi di belakang frame.');

        } catch (\Exception $e) {
            Log::error("SNAP CONTROLLER ERROR: " . $e->getMessage());
            
            $errorMsg = $e->getMessage();
            if ($e->getPrevious()) {
                $errorMsg .= " | " . $e->getPrevious()->getMessage();
            }
            return back()->withErrors(['msg' => $errorMsg])->withInput();
        }
    }
}