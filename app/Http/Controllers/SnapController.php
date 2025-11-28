<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image; // Pastikan ini ada

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
     * TAHAP 1: Terima Foto -> RESIZE KECIL -> Kirim ke Editor
     */
    public function showEditor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required',
            'frame_id' => 'required|exists:frames,id',
            'photos' => 'required|array', 
            'photos.*' => 'image|max:10240',
        ]);

        $frame = Frame::find($request->frame_id);
        $uploadedPhotos = $request->file('photos');

        if (count($uploadedPhotos) !== $frame->max_photos) {
            return back()
                ->withErrors(['msg' => "Gagal! Frame '{$frame->name}' butuh {$frame->max_photos} foto."])
                ->withInput();
        }

        try {
            $this->configureOCI();

            $photoUrls = [];
            
            foreach ($uploadedPhotos as $index => $photo) {
                $filename = 'temp_' . time() . '_' . $index . '_' . Str::random(8) . '.' . $photo->getClientOriginalExtension();
                
                // --- RESIZE AGRESIF (600px) ---
                // Kita kecilkan foto jadi Maksimal Lebar 600px.
                // Ini membuat foto sangat ringan dan dimensinya pas untuk web.
                $img = Image::make($photo);
                
                $img->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio(); 
                    $constraint->upsize();      
                });

                // Stream hasil resize
                $resource = $img->stream(null, 80); 
                Storage::disk('oci')->put('temp/' . $filename, $resource);
                
                // Generate URL
                $ociEndpoint = rtrim(config('filesystems.disks.oci.endpoint'), '/');
                $ociBucket = config('filesystems.disks.oci.bucket');
                $url = "{$ociEndpoint}/{$ociBucket}/temp/{$filename}";
                
                $photoUrls[] = $url;
            }

            $coordinates = json_decode($frame->coordinates, true);

            return view('gallery.editor', [
                'frame' => $frame,
                'photoUrls' => $photoUrls,
                'coordinates' => $coordinates,
                'requestData' => $request->only(['name', 'priority']) 
            ]);

        } catch (\Exception $e) {
            Log::error("SHOW EDITOR ERROR: " . $e->getMessage());
            return back()->withErrors(['msg' => 'Gagal memuat editor: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * TAHAP 2: Simpan Hasil Akhir
     */
    public function saveResult(Request $request)
    {
        $request->validate([
            'image_data' => 'required',
            'frame_id' => 'required',
            'name' => 'required',
            'priority' => 'required',
        ]);

        try {
            $user = Auth::user();
            $this->configureOCI();

            // Decode Base64
            $image_64 = $request->image_data; 
            if (strpos($image_64, 'data:image') === false) throw new \Exception("Format gambar tidak valid.");
            
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            $image = str_replace($replace, '', $image_64); 
            $image = str_replace(' ', '+', $image); 
            
            $imageName = 'FINAL_' . time() . '_' . Str::random(8) . '.' . $extension;

            // Simpan
            Storage::disk('oci')->put('results/' . $imageName, base64_decode($image));

            $ociEndpoint = rtrim(config('filesystems.disks.oci.endpoint'), '/');
            $ociBucket = config('filesystems.disks.oci.bucket');
            $finalResultUrl = "{$ociEndpoint}/{$ociBucket}/results/{$imageName}";

            Job::create([
                'user_id' => $user->id,
                'frame_id' => $request->frame_id,
                'name' => $request->name,
                'priority' => $request->priority,
                'original_file' => 'results/' . $imageName,
                'status' => 'COMPLETE',
                'result_url' => $finalResultUrl
            ]);

            return response()->json(['status' => 'success', 'redirect' => route('dashboard')]);

        } catch (\Exception $e) {
            Log::error("SAVE RESULT ERROR: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

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
    }
}