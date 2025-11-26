@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-4">Galeri Frame</h1>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                Pilih desain bingkai eksklusif kami dan abadikan momen terbaikmu.
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-4 mb-10">
            <button onclick="filterSelection('all')" id="btn-all" class="filter-btn px-6 py-2 rounded-full font-medium shadow-md transition-all duration-300 bg-blue-600 text-white transform scale-105">
                Semua
            </button>
            <button onclick="filterSelection('minimalis')" id="btn-minimalis" class="filter-btn px-6 py-2 rounded-full font-medium shadow-sm transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100">
                Minimalis
            </button>
            <button onclick="filterSelection('modern')" id="btn-modern" class="filter-btn px-6 py-2 rounded-full font-medium shadow-sm transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100">
                Modern
            </button>
            <button onclick="filterSelection('vintage')" id="btn-vintage" class="filter-btn px-6 py-2 rounded-full font-medium shadow-sm transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100">
                Vintage
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
            
            @forelse($frames as $frame)
            <div class="frame-item group bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                 data-name="{{ strtolower($frame->name) }} {{ strtolower($frame->description) }}">
                
                <div class="relative h-64 bg-gray-100 overflow-hidden group-hover:opacity-90 transition">
                    @if($frame->image_url)
                        <img src="{{ $frame->image_url }}" alt="{{ $frame->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-sm font-medium">No Preview</span>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/90 backdrop-blur text-xs font-bold px-3 py-1 rounded-full shadow-sm text-gray-800">
                            READY
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition">{{ $frame->name }}</h3>
                    <p class="text-sm text-gray-500 mb-6 line-clamp-2">
                        {{ $frame->description ?? 'Frame keren untuk koleksi foto kamu.' }}
                    </p>

                    <a href="{{ route('upload.create', ['frame_id' => $frame->id]) }}" class="block w-full py-3 px-4 bg-gray-900 hover:bg-blue-600 text-white text-center font-bold rounded-xl shadow-lg transition duration-300 flex items-center justify-center group-hover:shadow-blue-500/30">
                        <span>Pakai Frame Ini</span>
                        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
            @empty
            
            <div class="col-span-full flex flex-col items-center justify-center py-10 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Frame</h3>
                <p class="text-gray-500">Database frame masih kosong.</p>
            </div>
            @endforelse

        </div>

        <div class="border-t border-gray-200 pt-10 text-center">
            <p class="text-gray-500 mb-4">Tidak menemukan frame yang Anda cari?</p>
            <button type="button" class="px-8 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900 transition shadow-sm transform hover:-translate-y-0.5">
                Request Frame Baru
            </button>
        </div>

    </div>
</div>

<script>
    function filterSelection(category) {
        var x, i;
        x = document.getElementsByClassName("frame-item");
        if (category == "all") category = "";
        for (i = 0; i < x.length; i++) {
            w3RemoveClass(x[i], "hidden");
            var frameData = x[i].getAttribute('data-name');
            if (frameData.indexOf(category) == -1) {
                w3AddClass(x[i], "hidden");
            }
        }
        updateActiveButton(category);
    }

    function w3AddClass(element, name) {
        var arr1 = element.className.split(" ");
        if (arr1.indexOf(name) == -1) {
            element.className += " " + name;
        }
    }

    function w3RemoveClass(element, name) {
        element.className = element.className.replace(new RegExp('(?:^|\\s)'+name+'(?!\\S)'), '');
    }

    function updateActiveButton(activeCategory) {
        var btns = document.getElementsByClassName("filter-btn");
        for (var i = 0; i < btns.length; i++) {
            // Hapus kelas warna Biru (bg-blue-600) dari semua tombol
            btns[i].classList.remove("bg-blue-600", "text-white", "scale-105");
            btns[i].classList.add("bg-white", "text-gray-600");
        }
        
        // Tambahkan kelas warna Biru ke tombol yang aktif
        var activeBtnId = "btn-" + (activeCategory === "" ? "all" : activeCategory);
        var activeBtn = document.getElementById(activeBtnId);
        if(activeBtn) {
            activeBtn.classList.remove("bg-white", "text-gray-600");
            activeBtn.classList.add("bg-blue-600", "text-white", "scale-105");
        }
    }
</script>
@endsection