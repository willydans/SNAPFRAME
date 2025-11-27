<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
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

    /**
     * TAHAP 1: Terima Foto dari Form Upload -> Kirim ke Halaman Editor
     * Menggantikan fungsi store() yang lama
     */
    public function showEditor(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required',
            'frame_id' => 'required|exists:frames,id',
            'photos' => 'required|array', 
            'photos.*' => 'image|max:10240', // Max 10MB biar user leluasa
        ]);

        $frame = Frame::find($request->frame_id);
        $uploadedPhotos = $request->file('photos');

        // Validasi Jumlah Foto
        if (count($uploadedPhotos) !== $frame->max_photos) {
            return back()
                ->withErrors(['msg' => "Gagal! Frame '{$frame->name}' butuh {$frame->max_photos} foto."])
                ->withInput();
        }

        try {
            // Setup Koneksi OCI (Pakai fungsi helper biar rapi)
            $this->configureOCI();

            // 2. Upload Foto Mentah ke Folder 'temp' di OCI
            // Kita butuh URL publiknya untuk ditampilkan di Canvas JS
            $photoUrls = [];
            
            foreach ($uploadedPhotos as $index => $photo) {
                // Nama file unik
                $filename = 'temp_' . time() . '_' . $index . '_' . Str::random(8) . '.' . $photo->getClientOriginalExtension();
                
                // Upload
                Storage::disk('oci')->putFileAs('temp', $photo, $filename);
                
                // Generate Public URL
                $ociEndpoint = rtrim(config('filesystems.disks.oci.endpoint'), '/');
                $ociBucket = config('filesystems.disks.oci.bucket');
                $url = "{$ociEndpoint}/{$ociBucket}/temp/{$filename}";
                
                $photoUrls[] = $url;
            }

            // 3. Ambil Koordinat Frame
            $coordinates = json_decode($frame->coordinates, true);

            // 4. Lempar data ke View 'gallery.editor'
            return view('gallery.editor', [
                'frame' => $frame,
                'photoUrls' => $photoUrls,
                'coordinates' => $coordinates,
                // Kita kirim balik input nama/prioritas agar tidak hilang
                'requestData' => $request->only(['name', 'priority']) 
            ]);

        } catch (\Exception $e) {
            Log::error("SHOW EDITOR ERROR: " . $e->getMessage());
            return back()->withErrors(['msg' => 'Gagal memuat editor: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * TAHAP 2: Simpan Hasil Akhir dari Canvas (Base64)
     */
    public function saveResult(Request $request)
    {
        $request->validate([
            'image_data' => 'required', // String Base64 dari Canvas
            'frame_id' => 'required',
            'name' => 'required',
            'priority' => 'required',
        ]);

        try {
            $user = Auth::user();
            
            // Setup Koneksi OCI
            $this->configureOCI();

            // 1. Decode Base64 Image
            $image_64 = $request->image_data; 
            
            // Validasi format base64
            if (strpos($image_64, 'data:image') === false) {
                 throw new \Exception("Format gambar tidak valid.");
            }
            
            // Ekstrak ekstensi (jpg/png)
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
            // Bersihkan string base64
            $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            $image = str_replace($replace, '', $image_64); 
            $image = str_replace(' ', '+', $image); 
            
            // Buat nama file final
            $imageName = 'FINAL_' . time() . '_' . Str::random(8) . '.' . $extension;

            // 2. Simpan ke Folder 'results'
            Storage::disk('oci')->put('results/' . $imageName, base64_decode($image));

            // 3. Generate URL Akhir
            $ociEndpoint = rtrim(config('filesystems.disks.oci.endpoint'), '/');
            $ociBucket = config('filesystems.disks.oci.bucket');
            $finalResultUrl = "{$ociEndpoint}/{$ociBucket}/results/{$imageName}";

            // 4. Simpan ke Database
            Job::create([
                'user_id' => $user->id,
                'frame_id' => $request->frame_id,
                'name' => $request->name,
                'priority' => $request->priority,
                'original_file' => 'results/' . $imageName,
                'status' => 'COMPLETE',
                'result_url' => $finalResultUrl
            ]);

            // Return JSON untuk AJAX
            return response()->json(['status' => 'success', 'redirect' => route('dashboard')]);

        } catch (\Exception $e) {
            Log::error("SAVE RESULT ERROR: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper Private: Konfigurasi OCI yang Aman
     * Mencegah SignatureDoesNotMatch dan masalah Cache
     */
    private function configureOCI()
    {
        $ociKey = trim(env('OCI_ACCESS_KEY_ID', ''), " \"'");
        $ociSecret = trim(env('OCI_SECRET_ACCESS_KEY', ''), " \"'");
        $ociRegion = trim(env('OCI_DEFAULT_REGION', 'ap-batam-1'), " \"'");
        $ociBucket = trim(env('OCI_BUCKET_INPUT', ''), " \"'");
        $ociEndpoint = trim(env('OCI_URL', ''), " \"'");

        if (empty($ociKey) || empty($ociSecret) || empty($ociBucket)) {
             throw new \Exception("Konfigurasi ENV OCI belum lengkap.");
        }

        // Reset Config Disk secara Runtime
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
        
        // Hapus cache instance disk lama
        Storage::forgetDisk('oci');
    }
}