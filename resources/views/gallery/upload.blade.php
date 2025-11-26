@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">
        <!-- Judul / Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Upload Foto</h1>
            <p class="mt-2 text-gray-600 max-w-2xl">
                Upload foto-foto Anda dan buat koleksi yang menawan
            </p>
        </div>

        <!-- Card utama -->
        <!-- PERBAIKAN: Gunakan route 'upload.store' sesuai web.php -->
        <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Hidden Input untuk Frame ID (PENTING) -->
            <!-- Jika user sudah memilih frame dari galeri, kita simpan ID-nya di sini -->
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
                    
                    <!-- Jika belum pilih frame, tampilkan dropdown manual -->
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

                    <label for="photos" class="block">
                        <div id="drop-area" class="relative cursor-pointer rounded-lg border-2 border-dashed border-gray-200 bg-white p-8 flex flex-col items-center justify-center text-center transition hover:border-blue-300">
                            <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="absolute inset-0 opacity-0 w-full h-full cursor-pointer" required />
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h10a4 4 0 004-4V7a4 4 0 00-4-4H7a4 4 0 00-4 4v8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M12 16h.01" /></svg>
                                <div class="text-sm font-medium text-gray-700">Klik untuk upload atau drag & drop foto Anda</div>
                                <div class="text-xs text-gray-400">
                                    @if(isset($frame))
                                        Wajib upload <strong>{{ $frame->max_photos }} foto</strong>. Maksimal 5MB per foto.
                                    @else
                                        Sesuaikan jumlah foto dengan frame yang dipilih. Maks 5MB.
                                    @endif
                                </div>
                                <div id="upload-hint" class="text-xs text-red-500 hidden mt-2"></div>
                                <div id="file-info" class="text-sm text-gray-600 mt-2 hidden">
                                    <span id="file-count"></span> foto dipilih
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- Preview strip kecil -->
                    <div id="preview-strip" class="mt-4 grid grid-cols-3 sm:grid-cols-6 gap-2 hidden"></div>
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
                            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8" /></svg>
                            Proses Foto
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Request Frame -->
        <div class="max-w-2xl mx-auto text-center mt-8">
            <p class="text-sm text-gray-600 mb-3">Tidak menemukan frame yang Anda cari?</p>
            <a href="#" class="inline-flex items-center gap-2 px-5 py-2 border rounded-md bg-white border-gray-200 shadow-sm text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                Request Frame Khusus
            </a>
        </div>
    </div>
</div>

<!-- Script: drag & drop, preview, validation -->
@push('scripts')
<script>
    (function () {
        const dropArea = document.getElementById('drop-area');
        const input = document.getElementById('photos');
        const fileInfo = document.getElementById('file-info');
        const fileCount = document.getElementById('file-count');
        const previewStrip = document.getElementById('preview-strip');
        const uploadHint = document.getElementById('upload-hint');
        const MAX_SIZE = 5 * 1024 * 1024; // 5MB

        function resetPreview() {
            previewStrip.innerHTML = '';
            previewStrip.classList.add('hidden');
            fileInfo.classList.add('hidden');
            uploadHint.classList.add('hidden');
        }

        function showPreview(files) {
            previewStrip.innerHTML = '';
            let count = 0;
            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                const col = document.createElement('div');
                col.className = 'w-full h-20 rounded-md overflow-hidden border border-gray-100 bg-gray-50';
                reader.onload = (e) => {
                    col.style.backgroundImage = `url(${e.target.result})`;
                    col.style.backgroundSize = 'cover';
                    col.style.backgroundPosition = 'center';
                };
                reader.readAsDataURL(file);
                previewStrip.appendChild(col);
                count++;
            });
            
            if (count) {
                previewStrip.classList.remove('hidden');
                fileInfo.classList.remove('hidden');
                fileCount.textContent = `${count}`;
            } else {
                resetPreview();
            }
        }

        function validateFiles(files) {
            uploadHint.textContent = '';
            for (let i = 0; i < files.length; i++) {
                const f = files[i];
                if (!f.type.startsWith('image/')) {
                    uploadHint.textContent = 'Hanya file gambar yang diizinkan.';
                    return false;
                }
                if (f.size > MAX_SIZE) {
                    uploadHint.textContent = `Ukuran file tidak boleh lebih dari 5MB: ${f.name}`;
                    return false;
                }
            }
            return true;
        }

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('border-blue-300');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('border-blue-300');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('border-blue-300');
            const dt = e.dataTransfer;
            const files = dt.files;
            if (!validateFiles(files)) {
                uploadHint.classList.remove('hidden');
                return;
            }
            input.files = files;
            showPreview(files);
        });

        input.addEventListener('change', () => {
            const files = input.files;
            if (!files || !files.length) {
                resetPreview();
                return;
            }
            if (!validateFiles(files)) {
                uploadHint.classList.remove('hidden');
                return;
            }
            uploadHint.classList.add('hidden');
            showPreview(files);
        });

        document.querySelector('form').addEventListener('reset', resetPreview);
    })();
</script>
@endpush

@endsection