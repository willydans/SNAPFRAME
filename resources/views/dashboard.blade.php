@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-50">
    
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Halo, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="mt-1 text-gray-500">Siap berkarya hari ini?</p>
                </div>
                    <a href="{{ route('gallery') }}" class="inline-flex items-center px-5 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-transform transform hover:scale-105">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Pilih Frame & Upload
                    </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100 p-6 flex items-center transition hover:shadow-lg">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Projek</p>
                    <p class="text-2xl font-bold text-gray-900">12 Frame</p>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100 p-6 flex items-center transition hover:shadow-lg">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Status Server</p>
                    <p class="text-2xl font-bold text-gray-900">Online</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-2xl border border-gray-100 p-6 flex items-center transition hover:shadow-lg">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Terakhir Login</p>
                    <p class="text-sm font-bold text-gray-900">{{ now()->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-48 h-48 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <img src="https://ouch-cdn2.icons8.com/ezaf-1_5X0jT4u4t33G5t4_1-1.png" alt="Empty" class="w-32 opacity-75">
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada karya nih!</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-8">
                Ruang galeri kamu masih kosong melompong. Yuk, mulai upload foto pertamamu dan lihat keajaibannya.
            </p>
            <button class="px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                Jelajahi Frame Publik
            </button>
        </div>

    </div>
</div>
@endsection