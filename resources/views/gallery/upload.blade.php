@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">
        <!-- Judul / Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Upload Foto</h1>
            <p class="mt-2 text-gray-600 max-w-2xl">
                Upload foto-foto Anda dan sesuaikan posisinya di editor.
            </p>
        </div>

        <!-- Card utama -->
        <form action="{{ route('upload.editor') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Hidden Input untuk Frame ID -->
            @if(isset($selectedFrameId))
                <input type="hidden" name="frame_id" value="{{ $selectedFrameId }}">
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-700">Frame Terpilih</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih frame terlebih dahulu dari galeri</p>
                </div>

                <!-- Frame Terpilih -->
                <div class="p-6 border-b border-gray-100">
                    <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div id="selected-frame-preview" class="w-28 h-20 bg-white rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 overflow-hidden relative">
                                @if(isset($selectedFrameId))
                                    @php $frame = $frames->find($selectedFrameId); @endphp
                                    @if($frame)
                                        <img src="{{ $frame->image_url }}" class="w-full h-full object-contain p-1">
                                    @endif
                                @else
                                    <span class="text-sm">Belum ada frame</span>
                                @endif
                            </div>
                            <div>
                                @if(isset($selectedFrameId) && $frame)
                                    <p id="selected-frame-title" class="font-semibold text-gray-700">{{ $frame->name }}</p>
                                    <p class="text-sm text-gray-500">Kapasitas: <span class="font-bold text-blue-600">{{ $frame->max_photos }} Foto</span></p>
                                @else
                                    <p id="selected-frame-title" class="font-semibold text-gray-700">Belum memilih frame</p>
                                    <p class="text-sm text-gray-500">Pilih frame dari galeri untuk memulai proses</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('gallery') }}" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-200 text-sm bg-white text-gray-700 hover:bg-gray-50">
                                Ganti Frame
                            </a>
                        </div>
                    </div>
                    
                    @if(!isset($selectedFrameId))
                    <div class="mt-4">
                        <label class="block mb-2 text-sm font-medium text-gray-600">Atau Pilih Frame di Sini:</label>
                        <select name="frame_id" class="w-full px-4 py-2 border rounded-lg text-gray-900 border-gray-200 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Frame --</option>
                            @foreach($frames as $f)
                                <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->max_photos }} Foto)</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <!-- Upload Foto -->
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-700 mb-3">Upload Foto</h3>

                    <!-- Drop Area -->
                    <div id="drop-area" class="relative cursor-pointer rounded-lg border-2 border-dashed border-gray-200 bg-white p-8 flex flex-col items-center justify-center text-center transition hover:border-blue-300">
                        <!-- Input file disembunyikan tapi tetap berfungsi -->
                        <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                        
                        <div class="flex flex-col items-center gap-3 pointer-events-none">
                            <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h10a4 4 0 004-4V7a4 4 0 00-4-4H7a4 4 0 00-4 4v8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M12 16h.01" /></svg>
                            <div class="text-sm font-medium text-gray-700">Klik untuk tambah foto atau drag & drop di sini</div>
                            <div class="text-xs text-gray-400">
                                @if(isset($frame))
                                    Butuh <strong>{{ $frame->max_photos }} foto</strong>. Maksimal 10MB per foto.
                                @else
                                    Maksimal 10MB per foto.
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pesan Error / Info -->
                    <div id="upload-hint" class="text-sm text-red-500 hidden mt-3 font-medium"></div>
                    
                    <div id="file-info" class="text-sm text-gray-600 mt-3 hidden flex items-center justify-between bg-blue-50 p-2 rounded-md">
                        <span><span id="file-count" class="font-bold text-blue-600">0</span> foto terpilih</span>
                        <button type="button" id="clear-all-btn" class="text-xs text-red-500 hover:text-red-700 font-semibold underline">Hapus Semua</button>
                    </div>

                    <!-- Preview Grid (Tempat foto muncul) -->
                    <!-- Pastikan tidak ada karakter aneh di dalam div ini -->
                    <div id="preview-strip" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 hidden"></div>
                </div>

                <!-- Detail Pekerjaan -->
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-700 mb-4">Detail Pekerjaan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-600">Nama Pekerjaan</label>
                            <input id="name" name="name" type="text" placeholder="Misal: Liburan Keluarga 2024" class="w-full px-4 py-2 border rounded-lg text-gray-900 border-gray-200 focus:ring-2 focus:ring-blue-500" required />
                        </div>

                        <div>
                            <label for="priority" class="block mb-2 text-sm font-medium text-gray-600">Prioritas</label>
                            <select id="priority" name="priority" class="w-full px-4 py-2 border rounded-lg text-gray-900 border-gray-200 focus:ring-2 focus:ring-blue-500">
                                <option value="normal">Normal (3-5 hari)</option>
                                <option value="fast">Cepat (1-2 hari)</option>
                                <option value="express">Ekspres (6-12 jam)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('gallery') }}" class="inline-flex items-center px-4 py-3 border border-gray-200 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            ← Kembali ke Galeri
                        </a>
                        <button type="submit" id="process-btn" class="inline-flex items-center px-5 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Lanjut Edit Posisi
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="max-w-2xl mx-auto text-center mt-8">
            <p class="text-sm text-gray-600 mb-3">Tidak menemukan frame yang Anda cari?</p>
            <a href="#" class="inline-flex items-center gap-2 px-5 py-2 border rounded-md bg-white border-gray-200 shadow-sm text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                Request Frame Khusus
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropArea = document.getElementById('drop-area');
        const input = document.getElementById('photos');
        const fileInfo = document.getElementById('file-info');
        const fileCount = document.getElementById('file-count');
        const previewStrip = document.getElementById('preview-strip');
        const uploadHint = document.getElementById('upload-hint');
        const clearBtn = document.getElementById('clear-all-btn');
        const MAX_SIZE = 10 * 1024 * 1024; // 10MB

        // Menggunakan DataTransfer untuk menyimpan state file
        let dt = new DataTransfer();

        function updateUI() {
            previewStrip.innerHTML = '';
            uploadHint.classList.add('hidden');
            
            // 1. Update Input File Asli (PENTING: ini yang dikirim ke server)
            input.files = dt.files;

            // 2. Tampilkan Info Jumlah
            if (dt.files.length > 0) {
                fileInfo.classList.remove('hidden');
                previewStrip.classList.remove('hidden'); // Tampilkan container preview
                fileCount.textContent = dt.files.length;
            } else {
                fileInfo.classList.add('hidden');
                previewStrip.classList.add('hidden');
            }

            // 3. Generate Preview Items
            Array.from(dt.files).forEach((file, index) => {
                const reader = new FileReader();
                
                // Container Item
                const item = document.createElement('div');
                // Pakai h-32 w-full agar pasti muncul ukurannya (fallback kalau aspect-square ga jalan)
                item.className = 'relative group rounded-lg overflow-hidden border border-gray-200 bg-gray-50 shadow-sm h-32 w-full';
                
                // Gambar
                const img = document.createElement('div');
                img.className = 'w-full h-full bg-cover bg-center transition-transform group-hover:scale-105 duration-300';
                
                // Tombol Hapus (X)
                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-md opacity-90 hover:opacity-100 z-20 cursor-pointer';
                deleteBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                
                // Event Hapus per Item
                deleteBtn.onclick = (e) => {
                    e.stopPropagation(); // Cegah event bubbling
                    e.preventDefault();
                    removeFile(index);
                };

                reader.onload = (e) => {
                    img.style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(file);

                item.appendChild(img);
                item.appendChild(deleteBtn);
                previewStrip.appendChild(item);
            });
        }

        function removeFile(indexToRemove) {
            const newDt = new DataTransfer();
            Array.from(dt.files).forEach((file, i) => {
                if (i !== indexToRemove) {
                    newDt.items.add(file);
                }
            });
            dt = newDt;
            updateUI();
        }

        function handleFiles(files) {
            let hasError = false;
            
            Array.from(files).forEach(file => {
                // Validasi Tipe
                if (!file.type.startsWith('image/')) {
                    uploadHint.textContent = 'Hanya file gambar yang diizinkan.';
                    uploadHint.classList.remove('hidden');
                    hasError = true;
                    return;
                }
                // Validasi Ukuran
                if (file.size > MAX_SIZE) {
                    uploadHint.textContent = `Ukuran file terlalu besar (>10MB): ${file.name}`;
                    uploadHint.classList.remove('hidden');
                    hasError = true;
                    return;
                }
                
                // Tambahkan ke list (Accumulative)
                dt.items.add(file);
            });

            if (!hasError) {
                updateUI();
            }
        }

        // --- Event Listeners ---

        input.addEventListener('change', () => {
            if (input.files && input.files.length > 0) {
                handleFiles(input.files);
            }
        });

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('border-blue-400', 'bg-blue-50');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('border-blue-400', 'bg-blue-50');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('border-blue-400', 'bg-blue-50');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                handleFiles(e.dataTransfer.files);
            }
        });

        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            dt = new DataTransfer();
            updateUI();
        });

        // Reset form handling
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('reset', () => {
                setTimeout(() => { // Tunggu native reset selesai
                    dt = new DataTransfer();
                    updateUI();
                }, 10);
            });
        }
    });
</script>
@endpush

@endsection