@extends('layout')

@section('content')
<div class="space-y-8">
    
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Galeri Frame</h1>
        <p class="mt-2 text-gray-600">Pilih layout frame yang sempurna untuk koleksi foto Anda</p>
    </div>

    <!-- Grid Frame -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        @forelse($frames as $frame)
        <!-- Card Frame Item -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 {{ $loop->first ? 'ring-2 ring-yellow-400' : '' }}"> <!-- Efek border kuning untuk item pertama seperti di gambar -->
            
            <!-- Preview Image Container -->
            <div class="bg-gray-100 aspect-[4/3] flex items-center justify-center p-4">
                <img src="{{ $frame->image_url }}" alt="{{ $frame->name }}" class="object-contain max-h-full rounded-lg shadow-sm">
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900">{{ $frame->name }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $frame->description ?? 'Layout frame estetik untuk momen spesial Anda' }}</p>
                
                <div class="mt-6">
                    <a href="{{ route('upload.create', ['frame_id' => $frame->id]) }}" class="block w-full text-center py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        Gunakan Frame Ini
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-500">
            Belum ada frame tersedia. Silakan hubungi admin.
        </div>
        @endforelse

    </div>

    <!-- Footer Request Frame -->
    <div class="flex flex-col items-center justify-center pt-8 pb-4">
        <p class="text-gray-500 mb-4">Tidak menemukan frame yang Anda cari?</p>
        <button class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Request Frame Khusus
        </button>
    </div>
</div>
@endsection