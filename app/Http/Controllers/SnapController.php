<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Wajib import ini untuk fitur Login

class SnapController extends Controller
{
    /**
     * Menampilkan Halaman Galeri Frame (Halaman Utama / Home).
     * Menampilkan daftar semua frame yang tersedia.
     */
    public function gallery()
    {
        $frames = Frame::all();
        return view('frames.index', compact('frames'));
    }

    /**
     * Menampilkan Halaman Dashboard (Pekerjaan Saya).
     * Mengambil data job milik user yang sedang login saja.
     */
    public function index()
    {
        // Ambil ID user yang sedang login
        $userId = Auth::id();

        // Ambil data Job milik user tersebut, urutkan terbaru
        $jobs = Job::with('frame')
                    ->where('user_id', $userId) 
                    ->latest()
                    ->get();

        return view('dashboard', compact('jobs'));
    }

    /**
     * Menampilkan Halaman Form Upload.
     * Bisa menerima parameter 'frame_id' jika user memilih dari galeri.
     */
    public function create(Request $request)
    {
        $frames = Frame::all();
        
        // Cek apakah ada frame tertentu yang dipilih dari halaman galeri
        $selectedFrameId = $request->query('frame_id'); 

        return view('upload', compact('frames', 'selectedFrameId'));
    }

    /**
     * Menangani Proses Submit Form:
     * 1. Validasi Input
     * 2. Upload Foto ke Oracle Cloud (OCI)
     * 3. Simpan data ke Database MySQL
     */
    public function store(Request $request)
    {
        // 1. Validasi Input sesuai Desain UI
        $request->validate([
            'name' => 'required|string|max:255',         // Nama Pekerjaan
            'priority' => 'required|in:Normal,Tinggi',   // Prioritas
            'frame_id' => 'required|exists:frames,id',   // Frame yang dipilih
            'photo' => 'required|image|max:5120',        // Foto (Maks 5MB)
        ]);

        // 2. Proses Upload ke OCI Object Storage
        $file = $request->file('photo');
        
        // Buat nama file unik
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Upload menggunakan disk 'oci'
        try {
            $path = Storage::disk('oci')->putFileAs('input-photos', $file, $filename);
        } catch (\Exception $e) {
            // Jika upload gagal (misal koneksi internet putus)
            return back()->withErrors(['msg' => 'Gagal koneksi ke Cloud Storage: ' . $e->getMessage()]);
        }

        // 3. Simpan Data Transaksi ke Database
        Job::create([
            'user_id' => Auth::id(),        // PENTING: Gunakan ID user yang sedang login
            'frame_id' => $request->frame_id,
            'name' => $request->name,       
            'priority' => $request->priority, 
            'original_file' => $path,       // Path file di Cloud
            'status' => 'PENDING',          
        ]);

        // 4. Redirect ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Pekerjaan berhasil dibuat! Foto sedang dikirim ke Cloud.');
    }
}