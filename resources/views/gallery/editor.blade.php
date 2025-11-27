@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-900 py-8 px-4 flex flex-col items-center justify-center">
    
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Editor Foto</h1>
                <p class="text-sm text-gray-500">Geser & Zoom foto. Klik slot untuk memilih foto.</p>
            </div>
            <div class="text-sm text-blue-600 font-medium">
                Scroll mouse untuk Zoom
            </div>
        </div>

        <!-- Canvas Container -->
        <div class="relative bg-gray-200 w-full overflow-hidden flex justify-center items-center p-4" style="min-height: 600px;">
            <!-- Canvas -->
            <canvas id="editorCanvas" class="shadow-lg cursor-move bg-white"></canvas>
            
            <!-- Loading Indicator -->
            <div id="loading" class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-white z-50">
                <svg class="animate-spin h-10 w-10 mb-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p>Memuat Aset...</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <a href="{{ route('upload.create') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition">
                Batal
            </a>
            <button id="saveBtn" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition flex items-center gap-2">
                <span>Simpan Hasil</span>
                <svg id="btnSpinner" class="animate-spin h-4 w-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </button>
        </div>
    </div>
</div>

<!-- DATA DARI LARAVEL KE JS -->
<script>
    const FRAME_URL = "{{ $frame->image_url }}"; // URL Frame PNG
    // URL Foto user dari OCI temp folder
    const USER_PHOTOS = @json($photoUrls); 
    // Koordinat slot (x, y, w, h)
    const COORDINATES = @json($coordinates); 
    
    // Data form untuk disubmit balik
    const FORM_DATA = {
        frame_id: "{{ $frame->id }}",
        name: "{{ $requestData['name'] }}",
        priority: "{{ $requestData['priority'] }}",
        _token: "{{ csrf_token() }}"
    };
</script>

<script>
    // --- CORE EDITOR LOGIC ---
    const canvas = document.getElementById('editorCanvas');
    const ctx = canvas.getContext('2d');
    const loadingDiv = document.getElementById('loading');
    
    let frameImg = new Image();
    // Array objek foto: { img, x, y, width, height, scale, isDragging, startX, startY }
    let photoObjects = []; 
    let isLoaded = false;
    let selectedPhotoIndex = -1;

    // A. INISIALISASI
    frameImg.crossOrigin = "Anonymous"; // Penting agar bisa export canvas
    frameImg.src = FRAME_URL;
    
    frameImg.onload = () => {
        // Set ukuran canvas sesuai ukuran asli frame (resolusi tinggi)
        canvas.width = frameImg.width;
        canvas.height = frameImg.height;
        
        // CSS agar canvas fit di layar (responsive)
        canvas.style.maxWidth = '100%';
        canvas.style.height = 'auto';

        loadUserPhotos();
    };

    frameImg.onerror = () => {
        alert("Gagal memuat gambar Frame. Cek CORS atau URL.");
    };

    function loadUserPhotos() {
        let loadedCount = 0;
        
        USER_PHOTOS.forEach((url, index) => {
            const img = new Image();
            img.crossOrigin = "Anonymous";
            img.src = url;
            
            img.onload = () => {
                // Ambil koordinat slot untuk foto ini
                const slot = COORDINATES[index] || {x:0, y:0, w:300, h:300};
                
                // --- PERBAIKAN LOGIC SCALING ---
                // Kembali ke Math.max (Cover) agar foto memenuhi kotak.
                // Kita set multiplier 1.0 (pas) agar tidak terlalu zoom in.
                let scaleStart = Math.max(slot.w / img.width, slot.h / img.height) * 1.0;
                
                const finalW = img.width * scaleStart;
                const finalH = img.height * scaleStart;
                
                // Center foto di tengah slot
                const initX = slot.x - (finalW - slot.w) / 2;
                const initY = slot.y - (finalH - slot.h) / 2;

                photoObjects[index] = {
                    img: img,
                    x: initX,
                    y: initY,
                    width: finalW,
                    height: finalH,
                    origW: img.width,
                    origH: img.height,
                    scale: scaleStart,
                    slot: slot // Simpan batas slot untuk referensi
                };

                loadedCount++;
                if (loadedCount === USER_PHOTOS.length) {
                    isLoaded = true;
                    loadingDiv.classList.add('hidden');
                    draw();
                }
            };
        });
    }

    // B. FUNGSI GAMBAR (RENDER LOOP)
    function draw() {
        if (!isLoaded) return;

        // 1. Bersihkan Canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // 2. Isi Background Putih
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 3. Gambar Semua Foto User (Layer Bawah)
        photoObjects.forEach((photo, i) => {
            if(!photo) return;
            
            // --- HAPUS CLIPPING ---
            // Kita gambar foto apa adanya (bisa tumpang tindih, tapi ini yang diminta user)
            // agar tidak ada bagian yang terpotong/hilang.
            
            ctx.drawImage(photo.img, photo.x, photo.y, photo.width, photo.height);
            
            // Highlight foto yang sedang dipilih (Border Hijau)
            if (i === selectedPhotoIndex) {
                ctx.strokeStyle = "#00ff00";
                ctx.lineWidth = 4;
                ctx.strokeRect(photo.x, photo.y, photo.width, photo.height);
            }

            // Guide Border Slot (Hanya visual bantu, tidak memotong)
            if (i === selectedPhotoIndex) {
                ctx.save();
                ctx.strokeStyle = "rgba(255, 0, 0, 0.5)"; // Merah transparan
                ctx.lineWidth = 2;
                ctx.setLineDash([5, 5]); // Garis putus-putus
                ctx.strokeRect(photo.slot.x, photo.slot.y, photo.slot.w, photo.slot.h);
                ctx.restore();
            }
        });

        // 4. Gambar Frame PNG (Layer Atas)
        // Frame ini yang akan menutupi bagian foto yang "berantakan"
        ctx.drawImage(frameImg, 0, 0);
    }

    // C. INTERAKSI MOUSE / TOUCH
    let isDragging = false;
    let startX, startY;

    function getMousePos(evt) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        const clientX = evt.touches ? evt.touches[0].clientX : evt.clientX;
        const clientY = evt.touches ? evt.touches[0].clientY : evt.clientY;

        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    // 1. Mouse Down (Mulai Drag)
    function handleStart(evt) {
        if (!isLoaded) return;
        evt.preventDefault();
        
        const pos = getMousePos(evt);
        
        // LOGIKA SELEKSI YANG LEBIH PINTAR
        // Kita cek apakah user mengklik area SLOT?
        // Ini membantu memilih foto yang tepat meskipun fotonya tumpang tindih.
        let found = -1;
        
        for (let i = 0; i < photoObjects.length; i++) {
            const p = photoObjects[i];
            const s = p.slot;
            
            // Prioritas 1: Klik di dalam area SLOT
            if (pos.x >= s.x && pos.x <= s.x + s.w &&
                pos.y >= s.y && pos.y <= s.y + s.h) {
                found = i;
                break; 
            }
        }
        
        // Prioritas 2: Kalau tidak kena slot, cek kena gambar fotonya
        if (found === -1) {
             for (let i = 0; i < photoObjects.length; i++) {
                const p = photoObjects[i];
                if (pos.x >= p.x && pos.x <= p.x + p.width &&
                    pos.y >= p.y && pos.y <= p.y + p.height) {
                    found = i;
                }
            }
        }

        if (found !== -1) {
            selectedPhotoIndex = found;
            isDragging = true;
            startX = pos.x;
            startY = pos.y;
            draw();
        } else {
            selectedPhotoIndex = -1;
            draw();
        }
    }

    // 2. Mouse Move (Sedang Drag)
    function handleMove(evt) {
        if (!isDragging || selectedPhotoIndex === -1) return;
        evt.preventDefault();

        const pos = getMousePos(evt);
        const dx = pos.x - startX;
        const dy = pos.y - startY;

        const photo = photoObjects[selectedPhotoIndex];
        photo.x += dx;
        photo.y += dy;

        startX = pos.x;
        startY = pos.y;

        draw();
    }

    // 3. Mouse Up (Selesai Drag)
    function handleEnd(evt) {
        isDragging = false;
    }

    // 4. Zoom (Scroll Wheel)
    function handleScroll(evt) {
        if (selectedPhotoIndex === -1) return;
        evt.preventDefault();

        const photo = photoObjects[selectedPhotoIndex];
        const scaleFactor = 0.05;

        const delta = evt.deltaY > 0 ? -1 : 1; 
        const newScale = photo.scale + (delta * scaleFactor);

        if (newScale < 0.1 || newScale > 5) return;

        const oldW = photo.width;
        const oldH = photo.height;
        const newW = photo.origW * newScale;
        const newH = photo.origH * newScale;

        // Zoom centered
        photo.x -= (newW - oldW) / 2;
        photo.y -= (newH - oldH) / 2;
        
        photo.width = newW;
        photo.height = newH;
        photo.scale = newScale;

        draw();
    }

    // Event Listeners
    canvas.addEventListener('mousedown', handleStart);
    canvas.addEventListener('mousemove', handleMove);
    canvas.addEventListener('mouseup', handleEnd);
    canvas.addEventListener('mouseout', handleEnd);
    
    // Touch support (HP)
    canvas.addEventListener('touchstart', handleStart, {passive: false});
    canvas.addEventListener('touchmove', handleMove, {passive: false});
    canvas.addEventListener('touchend', handleEnd);
    
    // Zoom support
    canvas.addEventListener('wheel', handleScroll, {passive: false});


    // D. SAVE FUNCTION
    const saveBtn = document.getElementById('saveBtn');
    
    saveBtn.addEventListener('click', () => {
        if (!isLoaded) return;
        
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-75', 'cursor-not-allowed');
        document.getElementById('btnSpinner').classList.remove('hidden');

        selectedPhotoIndex = -1;
        draw(); // Redraw untuk menghilangkan border seleksi

        const dataURL = canvas.toDataURL('image/jpeg', 0.95);

        fetch("{{ route('upload.save-result') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": FORM_DATA._token
            },
            body: JSON.stringify({
                image_data: dataURL,
                frame_id: FORM_DATA.frame_id,
                name: FORM_DATA.name,
                priority: FORM_DATA.priority
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                alert("Error saat menyimpan: " + data.message);
                saveBtn.disabled = false;
                document.getElementById('btnSpinner').classList.add('hidden');
            }
        })
        .catch(error => {
            console.error(error);
            alert("Terjadi kesalahan jaringan.");
            saveBtn.disabled = false;
            document.getElementById('btnSpinner').classList.add('hidden');
        });
    });

</script>
@endsection