@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Halaman -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pekerjaan Saya</h1>
                <p class="mt-1 text-sm text-gray-500">Pantau statistik dan riwayat semua karya foto Anda di sini.</p>
            </div>
            
            <!-- Tombol Tambah -->
            <a href="{{ route('gallery') }}" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition hover:-translate-y-0.5">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Projek Baru
            </a>
        </div>

        <!-- STATISTIK (PINDAHAN DARI DASHBOARD) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Card 1: Total Karya -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 flex items-center transition hover:shadow-md">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Karya</p>
                    <!-- Fallback jika variabel totalProjek tidak dikirim controller, hitung dari jobs -->
                    <p class="text-2xl font-bold text-gray-900">{{ isset($totalProjek) ? $totalProjek : $jobs->count() }}</p>
                </div>
            </div>
            
            <!-- Card 2: Status Server -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 flex items-center transition hover:shadow-md">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status Server</p>
                    <p class="text-2xl font-bold text-gray-900">Online</p>
                </div>
            </div>

            <!-- Card 3: Waktu -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-6 flex items-center transition hover:shadow-md">
                <div class="p-3 rounded-full bg-purple-50 text-purple-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Waktu Sekarang</p>
                    <p class="text-sm font-bold text-gray-900">{{ now()->format('d M, H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- TABEL RIWAYAT PEKERJAAN -->
        @if($jobs->count() > 0)
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Daftar Riwayat</h3>
                    <span class="text-xs text-gray-500">{{ $jobs->count() }} item</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Preview</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Projek</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Prioritas</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($jobs as $job)
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4 whitespace-nowrap w-24">
                                    <div class="h-20 w-20 rounded-lg border border-gray-200 overflow-hidden bg-gray-100 relative group-hover:shadow-md transition">
                                        @if($job->result_url)
                                            <img class="h-full w-full object-cover" src="{{ $job->result_url }}" alt="Hasil Foto">
                                        @else
                                            <div class="flex items-center justify-center h-full text-gray-400 text-xs">Processing</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $job->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600">{{ $job->frame->name ?? 'Deleted Frame' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($job->priority == 'express')
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-red-50 text-red-700 border border-red-100">
                                            ⚡ Ekspres
                                        </span>
                                    @elseif($job->priority == 'fast')
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-100">
                                            🚀 Cepat
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-50 text-green-700 border border-green-100">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $job->created_at->format('d M Y') }}
                                    <div class="text-xs text-gray-400">{{ $job->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($job->result_url)
                                        <a href="{{ $job->result_url }}" target="_blank" class="text-white bg-gray-900 hover:bg-black px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition inline-flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                            Download
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Memproses...</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- EMPTY STATE -->
            <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-300 p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Workspace Kosong</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-8">
                    Belum ada riwayat pekerjaan. Pilih frame estetik dan mulai berkarya sekarang!
                </p>
                <a href="{{ route('gallery') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                    Lihat Galeri Frame
                </a>
            </div>
        @endif

    </div>
</div>
@endsection