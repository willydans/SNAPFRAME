<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan form upload (bisa menerima frame id via query -> ?frame=123)
     */
    public function create(Request $request)
    {
        $selectedFrame = null;
        if ($request->query('frame')) {
            // Ambil frame jika model App\Models\Frame ada
            if (class_exists(\App\Models\Frame::class)) {
                $selectedFrame = \App\Models\Frame::find($request->query('frame'));
            }
        }

        // Jika ada model Frame, berikan daftar frame untuk pilih; kalau tidak, kirim koleksi kosong
        $frames = class_exists(\App\Models\Frame::class) ? \App\Models\Frame::all() : collect();

        return view('gallery.upload', compact('frames', 'selectedFrame'));
    }

    /**
     * Simpan upload foto dan buat project/pekerjaan
     */
    public function store(Request $request)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:5120',
            'job_name' => 'nullable|string|max:255',
            'priority' => 'required|in:normal,fast,express',
            'frame_id' => 'nullable|integer'
        ]);

        $saved = [];
        foreach ($request->file('photos') as $file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('gallery', $filename, 'public');

            $saved[] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ];

            // Jika ada model Photo/Gallery, simpan record (opsional)
            if (class_exists(\App\Models\Photo::class)) {
                \App\Models\Photo::create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'user_id' => auth()->id(),
                    // tambahkan kolom sesuai model Photo milik Anda
                ]);
            }
        }

        // Jika Anda ingin menyimpan "pekerjaan/project" di DB, sesuaikan dengan model Anda
        if (class_exists(\App\Models\Job::class)) {
            $job = \App\Models\Job::create([
                'user_id' => auth()->id(),
                'name' => $request->input('job_name'),
                'priority' => $request->input('priority'),
                'frame_id' => $request->input('frame_id'),
                // dll
            ]);

            // Jika model JobPhoto/Photo relation ada, attach file paths ke job
            if (method_exists($job, 'photos')) {
                foreach ($saved as $s) {
                    $job->photos()->create([
                        'path' => $s['path'],
                        'original_name' => $s['original_name'],
                    ]);
                }
            }
        }

        return redirect()->route('gallery')->with('success', 'Foto berhasil diupload.');
    }

    /**
     * Tampilkan form request frame (halaman atau modal)
     *
     * Note: Jika Anda sudah punya halaman frames/index.blade.php
     * - Anda bisa menampilkan form ini sebagai modal di frames/index
     * - Atau pakai halaman terpisah resources/views/frames/request.blade.php
     */
    public function request()
    {
        return view('frames.request'); // Buat view ini jika belum ada
    }

    /**
     * Terima form request frame
     */
    public function submitRequest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'contact' => 'nullable|string|max:255',
        ]);

        // Jika ada model FrameRequest, simpan ke DB
        if (class_exists(\App\Models\FrameRequest::class)) {
            \App\Models\FrameRequest::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'details' => $request->details,
                'contact' => $request->contact,
                'status' => 'pending',
            ]);
        } else {
            // Jika tidak ada DB model, simpan sebagai file log (sementara)
            Storage::disk('local')->append('frame-requests.log', now() . ' | ' . auth()->id() . ' | ' . $request->title . ' | ' . $request->details);
        }

        return redirect()->route('gallery')->with('success', 'Request frame berhasil dikirim.');
    }
}