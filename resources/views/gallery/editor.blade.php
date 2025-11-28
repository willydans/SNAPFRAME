@extends('layout')

@section('content')
<!-- CSS CropperJS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<div class="min-h-screen bg-gray-900 py-8 px-4 flex flex-col items-center justify-center">
    
    <div class="w-full max-w-6xl bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- SIDEBAR KONTROL -->
        <div class="w-full md:w-80 bg-gray-50 border-r border-gray-200 p-5 flex flex-col gap-4 z-10 overflow-y-auto" style="max-height: 90vh;">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Editor Foto</h1>
                <p class="text-xs text-gray-500 mt-1">Klik foto di canvas untuk mengedit.</p>
            </div>

            <!-- PANEL 1: KONTROL FOTO TERPILIH (Hidden by default) -->
            <div id="photoActions" class="bg-white p-4 rounded-lg shadow-sm border border-blue-200 hidden transition-all space-y-4">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider border-b pb-2">Foto Terpilih</p>
                
                <!-- Tombol Crop -->
                <button id="openCropModalBtn" class="w-full py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold rounded shadow-sm flex items-center justify-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span>✂️ Pangkas / Crop</span>
                </button>

                <!-- Zoom Control -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Zoom (Besar/Kecil)</label>
                        <span id="zoomVal" class="text-[10px] text-blue-600 font-mono">1.0x</span>
                    </div>
                    <input type="range" id="zoomSlider" min="0.1" max="3" step="0.05" value="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                </div>

                <!-- Stretch Controls (FITUR BARU) -->
                <div class="pt-2 border-t border-dashed">
                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">Stretch / Tarik</p>
                    
                    <!-- Stretch Horizontal -->
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-[10px] text-gray-400">Lebar (X)</label>
                            <button id="resetStretchX" class="text-[9px] text-red-400 hover:text-red-600">Reset</button>
                        </div>
                        <input type="range" id="stretchXSlider" min="0.5" max="2" step="0.05" value="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-500">
                    </div>

                    <!-- Stretch Vertical -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-[10px] text-gray-400">Tinggi (Y)</label>
                            <button id="resetStretchY" class="text-[9px] text-red-400 hover:text-red-600">Reset</button>
                        </div>
                        <input type="range" id="stretchYSlider" min="0.5" max="2" step="0.05" value="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-500">
                    </div>
                </div>
            </div>

            <!-- PANEL 2: OPSI FRAME (Selalu Muncul) -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 space-y-4">
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider border-b pb-2">Opsi Frame</p>

                <!-- Transparansi Frame (FITUR DIKEMBALIKAN) -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Transparansi</label>
                        <span id="opacityVal" class="text-[10px] text-blue-600 font-mono">100%</span>
                    </div>
                    <input type="range" id="frameOpacity" min="0" max="1" step="0.1" value="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-gray-600">
                    <p class="text-[9px] text-gray-400 mt-1">*Geser jika foto tertutup frame putih</p>
                </div>

                <!-- Posisi Layer -->
                <div>
                    <label class="text-[10px] font-bold text-gray-500 uppercase mb-2 block">Posisi Foto</label>
                    <div class="flex gap-2">
                        <button id="layerBackBtn" class="flex-1 py-1.5 px-2 text-xs rounded bg-blue-600 text-white font-medium shadow-sm transition">
                            Belakang
                        </button>
                        <button id="layerFrontBtn" class="flex-1 py-1.5 px-2 text-xs rounded bg-gray-200 text-gray-700 font-medium transition">
                            Depan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="mt-auto pt-4 border-t space-y-2">
                <button id="saveBtn" class="w-full py-3 rounded-lg bg-green-600 text-white font-bold hover:bg-green-700 transition flex justify-center items-center gap-2">
                    <span>Simpan Hasil</span>
                    <svg id="btnSpinner" class="animate-spin h-4 w-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
                <a href="{{ route('upload.create') }}" class="block text-center text-sm text-gray-500 hover:text-gray-800">Batal & Kembali</a>
            </div>
        </div>

        <!-- AREA KANVAS -->
        <div class="flex-1 bg-gray-200 relative overflow-hidden flex justify-center items-center p-4 min-h-[600px]">
            <canvas id="editorCanvas" class="shadow-2xl cursor-move bg-white"></canvas>
            
            <div id="loading" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center text-white z-50">
                <svg class="animate-spin h-10 w-10 mb-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p>Memuat Foto...</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CROP -->
<div id="cropModal" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg overflow-hidden w-full max-w-2xl flex flex-col max-h-[90vh]">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-lg text-gray-800">Pangkas Foto</h3>
            <button id="closeCropBtn" class="text-gray-500 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex-1 bg-gray-900 overflow-hidden relative flex justify-center items-center p-4">
            <img id="imageToCrop" src="" class="max-w-full max-h-[60vh] block">
        </div>
        <div class="p-4 border-t bg-gray-50 flex justify-end gap-3">
            <button id="cancelCropBtn" class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded">Batal</button>
            <button id="applyCropBtn" class="px-6 py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 shadow">Terapkan</button>
        </div>
    </div>
</div>

<script>
    const FRAME_URL = "{{ $frame->image_url }}"; 
    const USER_PHOTOS = @json($photoUrls); 
    const COORDINATES = @json($coordinates); 
    const FORM_DATA = {
        frame_id: "{{ $frame->id }}",
        name: "{{ $requestData['name'] }}",
        priority: "{{ $requestData['priority'] }}",
        _token: "{{ csrf_token() }}"
    };
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    const canvas = document.getElementById('editorCanvas');
    const ctx = canvas.getContext('2d');
    const loadingDiv = document.getElementById('loading');
    
    // UI Controls
    const photoActions = document.getElementById('photoActions');
    
    // Photo Controls
    const zoomSlider = document.getElementById('zoomSlider');
    const zoomVal = document.getElementById('zoomVal');
    const stretchXSlider = document.getElementById('stretchXSlider');
    const stretchYSlider = document.getElementById('stretchYSlider');
    const resetStretchX = document.getElementById('resetStretchX');
    const resetStretchY = document.getElementById('resetStretchY');
    const openCropModalBtn = document.getElementById('openCropModalBtn');

    // Frame Controls
    const layerBackBtn = document.getElementById('layerBackBtn');
    const layerFrontBtn = document.getElementById('layerFrontBtn');
    const frameOpacityInput = document.getElementById('frameOpacity');
    const opacityVal = document.getElementById('opacityVal');

    // Crop Modal
    const cropModal = document.getElementById('cropModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const closeCropBtn = document.getElementById('closeCropBtn');
    const cancelCropBtn = document.getElementById('cancelCropBtn');
    const applyCropBtn = document.getElementById('applyCropBtn');

    // State
    let frameImg = new Image();
    let photoObjects = []; 
    let isLoaded = false;
    let selectedPhotoIndex = -1;
    let photosOnTop = false; 
    let frameAlpha = 1.0;
    let cropper = null;

    // --- INISIALISASI ---
    frameImg.crossOrigin = "Anonymous"; 
    frameImg.src = FRAME_URL;
    
    frameImg.onload = () => {
        canvas.width = frameImg.width;
        canvas.height = frameImg.height;
        
        const container = canvas.parentElement;
        const scale = Math.min((container.clientWidth - 40) / canvas.width, 1);
        canvas.style.width = `${canvas.width * scale}px`;
        canvas.style.height = `${canvas.height * scale}px`;

        loadUserPhotos();
    };

    frameImg.onerror = () => { alert("Gagal memuat gambar Frame."); };

    function loadUserPhotos() {
        let loadedCount = 0;
        
        USER_PHOTOS.forEach((url, index) => {
            const img = new Image();
            img.crossOrigin = "Anonymous";
            img.src = url;
            
            img.onload = () => {
                const slot = COORDINATES[index] || {x:0, y:0, w:300, h:300};
                
                // Logic Scaling Awal: Fit Inside 80% (Kecil & Aman)
                let scaleStart = Math.min(slot.w / img.width, slot.h / img.height) * 0.8;
                
                const finalW = img.width * scaleStart;
                const finalH = img.height * scaleStart;
                
                const initX = slot.x + (slot.w - finalW) / 2;
                const initY = slot.y + (slot.h - finalH) / 2;

                photoObjects[index] = {
                    img: img,
                    originalSrc: url, 
                    x: initX,
                    y: initY,
                    // Dimensi saat ini
                    width: finalW,
                    height: finalH,
                    // Dimensi asli
                    origW: img.width,
                    origH: img.height,
                    // Faktor Transformasi
                    scale: scaleStart,
                    stretchX: 1.0, // Default normal
                    stretchY: 1.0, // Default normal
                    slot: slot 
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

    // --- FUNGSI UPDATE DIMENSI (SENTRALISASI) ---
    // Dipanggil setiap kali Slider Zoom / Stretch berubah
    function updatePhotoDimensions(photo) {
        // Hitung lebar & tinggi baru berdasarkan:
        // Ukuran Asli * Zoom * Stretch
        
        const oldW = photo.width;
        const oldH = photo.height;
        
        const newW = photo.origW * photo.scale * photo.stretchX;
        const newH = photo.origH * photo.scale * photo.stretchY;
        
        // Simpan
        photo.width = newW;
        photo.height = newH;
        
        // Center Zoom (Agar membesar dari tengah)
        photo.x -= (newW - oldW) / 2;
        photo.y -= (newH - oldH) / 2;
    }

    // --- FUNGSI GAMBAR ---
    function draw() {
        if (!isLoaded) return;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const drawPhotos = () => {
            photoObjects.forEach((photo, i) => {
                if(!photo) return;
                ctx.drawImage(photo.img, photo.x, photo.y, photo.width, photo.height);
                
                // Highlight Seleksi
                if (i === selectedPhotoIndex) {
                    ctx.strokeStyle = "#00ff00";
                    ctx.lineWidth = 4;
                    ctx.strokeRect(photo.x, photo.y, photo.width, photo.height);
                }
            });
        };

        const drawFrame = () => {
            ctx.save();
            ctx.globalAlpha = frameAlpha; // Terapkan Transparansi
            ctx.drawImage(frameImg, 0, 0);
            ctx.restore();
        };

        if (photosOnTop) {
            drawFrame();
            drawPhotos();
        } else {
            drawPhotos();
            drawFrame();
        }

        // Guide Slot
        if (selectedPhotoIndex !== -1) {
            const p = photoObjects[selectedPhotoIndex];
            ctx.save();
            ctx.strokeStyle = "rgba(255, 0, 0, 0.5)"; 
            ctx.lineWidth = 2;
            ctx.setLineDash([10, 10]); 
            ctx.strokeRect(p.slot.x, p.slot.y, p.slot.w, p.slot.h);
            ctx.restore();
        }
    }

    // --- EVENT LISTENERS KONTROL ---

    // 1. ZOOM Slider
    zoomSlider.addEventListener('input', (e) => {
        if (selectedPhotoIndex === -1) return;
        const photo = photoObjects[selectedPhotoIndex];
        photo.scale = parseFloat(e.target.value);
        zoomVal.textContent = photo.scale.toFixed(2) + 'x';
        updatePhotoDimensions(photo);
        draw();
    });

    // 2. STRETCH Sliders
    stretchXSlider.addEventListener('input', (e) => {
        if (selectedPhotoIndex === -1) return;
        const photo = photoObjects[selectedPhotoIndex];
        photo.stretchX = parseFloat(e.target.value);
        updatePhotoDimensions(photo);
        draw();
    });

    stretchYSlider.addEventListener('input', (e) => {
        if (selectedPhotoIndex === -1) return;
        const photo = photoObjects[selectedPhotoIndex];
        photo.stretchY = parseFloat(e.target.value);
        updatePhotoDimensions(photo);
        draw();
    });

    // Reset Stretch Buttons
    resetStretchX.addEventListener('click', () => {
        if (selectedPhotoIndex === -1) return;
        stretchXSlider.value = 1;
        // Trigger event manual
        stretchXSlider.dispatchEvent(new Event('input'));
    });
    
    resetStretchY.addEventListener('click', () => {
        if (selectedPhotoIndex === -1) return;
        stretchYSlider.value = 1;
        stretchYSlider.dispatchEvent(new Event('input'));
    });

    // 3. FRAME OPTIONS
    frameOpacityInput.addEventListener('input', (e) => {
        frameAlpha = parseFloat(e.target.value);
        opacityVal.textContent = Math.round(frameAlpha * 100) + '%';
        draw();
    });

    layerBackBtn.addEventListener('click', () => {
        photosOnTop = false;
        updateLayerUI();
        draw();
    });
    layerFrontBtn.addEventListener('click', () => {
        photosOnTop = true;
        updateLayerUI();
        draw();
    });

    function updateLayerUI() {
        const activeClass = "bg-blue-600 text-white";
        const inactiveClass = "bg-gray-200 text-gray-700";
        
        if(photosOnTop) {
            layerFrontBtn.className = `flex-1 py-1.5 px-2 text-xs rounded font-medium shadow-sm transition ${activeClass}`;
            layerBackBtn.className = `flex-1 py-1.5 px-2 text-xs rounded font-medium transition ${inactiveClass}`;
        } else {
            layerBackBtn.className = `flex-1 py-1.5 px-2 text-xs rounded font-medium shadow-sm transition ${activeClass}`;
            layerFrontBtn.className = `flex-1 py-1.5 px-2 text-xs rounded font-medium transition ${inactiveClass}`;
        }
    }

    // --- CROPPER LOGIC (TETAP ADA) ---
    function openCropModal() {
        if (selectedPhotoIndex === -1) return;
        const photo = photoObjects[selectedPhotoIndex];
        imageToCrop.src = photo.img.src;
        cropModal.classList.remove('hidden');
        if (cropper) { cropper.destroy(); }
        cropper = new Cropper(imageToCrop, { viewMode: 1, autoCropArea: 0.8 });
    }

    function closeCropModal() {
        cropModal.classList.add('hidden');
        if (cropper) { cropper.destroy(); cropper = null; }
    }

    applyCropBtn.addEventListener('click', () => {
        if (!cropper || selectedPhotoIndex === -1) return;
        const croppedDataUrl = cropper.getCroppedCanvas().toDataURL('image/jpeg');
        const photo = photoObjects[selectedPhotoIndex];
        const newImg = new Image();
        
        newImg.onload = () => {
            photo.img = newImg;
            photo.origW = newImg.width;
            photo.origH = newImg.height;
            
            // Reset transforms post-crop
            const slot = photo.slot;
            let newScale = Math.min(slot.w / newImg.width, slot.h / newImg.height) * 0.9;
            
            photo.scale = newScale;
            photo.stretchX = 1.0;
            photo.stretchY = 1.0;
            
            updatePhotoDimensions(photo); // Recalculate w/h
            
            // Re-center
            photo.x = slot.x + (slot.w - photo.width) / 2;
            photo.y = slot.y + (slot.h - photo.height) / 2;

            // Sync Sliders
            zoomSlider.value = newScale;
            zoomVal.textContent = newScale.toFixed(2) + 'x';
            stretchXSlider.value = 1;
            stretchYSlider.value = 1;

            draw();
            closeCropModal();
        };
        newImg.src = croppedDataUrl;
    });

    openCropModalBtn.addEventListener('click', openCropModal);
    closeCropBtn.addEventListener('click', closeCropModal);
    cancelCropBtn.addEventListener('click', closeCropModal);

    // --- MOUSE HANDLERS (SELEKSI) ---
    let isDragging = false;
    let startX, startY;

    function getMousePos(evt) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const clientX = evt.touches ? evt.touches[0].clientX : evt.clientX;
        const clientY = evt.touches ? evt.touches[0].clientY : evt.clientY;
        return { x: (clientX - rect.left) * scaleX, y: (clientY - rect.top) * scaleY };
    }

    function handleStart(evt) {
        if (!isLoaded) return;
        evt.preventDefault();
        const pos = getMousePos(evt);
        let found = -1;
        
        // Cek Slot
        for (let i = 0; i < photoObjects.length; i++) {
            const s = photoObjects[i].slot;
            if (pos.x >= s.x && pos.x <= s.x + s.w && pos.y >= s.y && pos.y <= s.y + s.h) {
                found = i; break; 
            }
        }
        // Fallback Foto
        if (found === -1) {
             for (let i = 0; i < photoObjects.length; i++) {
                const p = photoObjects[i];
                if (pos.x >= p.x && pos.x <= p.x + p.width && pos.y >= p.y && pos.y <= p.y + p.height) {
                    found = i;
                }
            }
        }

        if (found !== -1) {
            selectedPhotoIndex = found;
            isDragging = true;
            startX = pos.x;
            startY = pos.y;
            
            // Sync UI dengan State Foto Terpilih
            const p = photoObjects[found];
            photoActions.classList.remove('hidden');
            zoomSlider.value = p.scale;
            zoomVal.textContent = p.scale.toFixed(2) + 'x';
            stretchXSlider.value = p.stretchX;
            stretchYSlider.value = p.stretchY;
            
            draw();
        } else {
            selectedPhotoIndex = -1;
            photoActions.classList.add('hidden');
            draw();
        }
    }

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

    function handleEnd() { isDragging = false; }

    function handleScroll(evt) {
        if (selectedPhotoIndex === -1) return;
        evt.preventDefault();
        const photo = photoObjects[selectedPhotoIndex];
        const delta = evt.deltaY > 0 ? -1 : 1; 
        let newScale = photo.scale + (delta * 0.05);
        if (newScale < 0.05) newScale = 0.05;
        if (newScale > 10) newScale = 10;
        
        photo.scale = newScale;
        zoomSlider.value = newScale;
        zoomVal.textContent = newScale.toFixed(2) + 'x';
        
        updatePhotoDimensions(photo);
        draw();
    }

    canvas.addEventListener('mousedown', handleStart);
    canvas.addEventListener('mousemove', handleMove);
    canvas.addEventListener('mouseup', handleEnd);
    canvas.addEventListener('mouseout', handleEnd);
    canvas.addEventListener('touchstart', handleStart, {passive: false});
    canvas.addEventListener('touchmove', handleMove, {passive: false});
    canvas.addEventListener('touchend', handleEnd);
    canvas.addEventListener('wheel', handleScroll, {passive: false});

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.addEventListener('click', () => {
        if (!isLoaded) return;
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-75');
        document.getElementById('btnSpinner').classList.remove('hidden');
        
        selectedPhotoIndex = -1;
        // Pastikan frame full opacity & di depan saat save agar rapi
        // photosOnTop = false; 
        // frameAlpha = 1.0; 
        // draw(); // Uncomment baris atas jika ingin hasil save selalu standard
        
        draw(); // Save sesuai apa yang user lihat (WYSIWYG)
        
        const dataURL = canvas.toDataURL('image/jpeg', 0.95);

        fetch("{{ route('upload.save-result') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": FORM_DATA._token },
            body: JSON.stringify({
                image_data: dataURL,
                frame_id: FORM_DATA.frame_id,
                name: FORM_DATA.name,
                priority: FORM_DATA.priority
            })
        })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') window.location.href = d.redirect;
            else { alert(d.message); saveBtn.disabled = false; }
        })
        .catch(e => { alert("Error jaringan."); saveBtn.disabled = false; });
    });
</script>
@endsection