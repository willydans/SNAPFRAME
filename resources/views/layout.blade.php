<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnapFrame OCI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 text-gray-800">
    
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Kiri: Logo -->
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-blue-600 italic" style="font-family: cursive;">SnapFrame</span>
                    </a>
                    
                    @auth
                    <!-- Menu Tengah (Hanya muncul jika login) -->
                    <div class="hidden sm:ml-10 sm:flex sm:space-x-8">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out
                           {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            Dashboard
                        </a>

                        <!-- Galeri Frame -->
                        <a href="{{ route('gallery') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out
                           {{ request()->routeIs('gallery') || request()->routeIs('home') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            Galeri Frame
                        </a>

                        <!-- Upload Foto -->
                        <!-- PERBAIKAN: Menggunakan route 'upload.create' sesuai web.php -->
                        <a href="{{ route('upload.create') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out
                           {{ request()->routeIs('upload.create') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            Upload Foto
                        </a>

                        <!-- Pekerjaan Saya -->
                        <a href="{{ route('pekerjaan') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out
                           {{ request()->routeIs('pekerjaan') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                            Pekerjaan Saya
                        </a>
                    </div>
                    @endauth
                </div>

                <!-- Kanan: User Menu -->
                <div class="flex items-center">
                    @auth
                        <div class="ml-3 relative flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-700">Halo, {{ Auth::user()->name }}</span>
                            
                            <!-- Tombol Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium transition duration-150 ease-in-out">Logout</button>
                            </form>
                            
                            <!-- Avatar Inisial -->
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                    @else
                        <div class="flex space-x-4"> 
                            <a href="{{ route('login') }}" 
                               class="text-sm font-semibold leading-6 transition duration-150 ease-in-out px-3 py-2 rounded-md
                               {{ request()->routeIs('login') ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:text-gray-900' }}">
                                Masuk
                            </a>

                            <a href="{{ route('register') }}" 
                               class="text-sm font-semibold leading-6 transition duration-150 ease-in-out px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-500 shadow-sm">
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Menampilkan Pesan Sukses/Error Global -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terdapat beberapa kesalahan:</h3>
                            <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    
    <footer class="bg-white border-t mt-12 py-8 text-center text-gray-400 text-sm">
        &copy; 2025 SnapFrame OCI Project.
    </footer>

    <!-- Script Toggle Password (Opsional untuk Login/Register) -->
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeOpen = button.querySelector('.eye-open');
            const eyeClosed = button.querySelector('.eye-closed');

            if (input.type === "password") {
                input.type = "text"; 
                if(eyeOpen) eyeOpen.classList.add('hidden'); 
                if(eyeClosed) eyeClosed.classList.remove('hidden'); 
            } else {
                input.type = "password"; 
                if(eyeOpen) eyeOpen.classList.remove('hidden');
                if(eyeClosed) eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>