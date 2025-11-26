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
                        <a href="{{ route('dashboard') }}" 
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out
                            {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                Dashboard
                        </a>
                        <a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') || request()->routeIs('home') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Galeri Frame
                        </a>
                        <a href="{{ route('gallery.upload.create') }}"
                            class="{{ request()->routeIs('gallery.upload.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Upload Foto
                        </a>
                        <a href="{{ route('pekerjaan') }}" class="{{ request()->routeIs('pekerjaan') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Logout</button>
                            </form>
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                    @else
                        <div class="flex space-x-8"> <a href="{{ route('login') }}" 
                            class="text-sm font-semibold leading-6 transition duration-150 ease-in-out
                            {{ request()->routeIs('login') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
                            Masuk
                            </a>

                            <a href="{{ route('register') }}" 
                            class="text-sm font-semibold leading-6 transition duration-150 ease-in-out
                            {{ request()->routeIs('register') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
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
            @yield('content')
        </div>
    </main>
    
    <footer class="bg-white border-t mt-12 py-8 text-center text-gray-400 text-sm">
        &copy; 2025 SnapFrame OCI Project.
    </footer>
        <script>
            function togglePassword(inputId, button) {
                const input = document.getElementById(inputId);
                const eyeOpen = button.querySelector('.eye-open');
                const eyeClosed = button.querySelector('.eye-closed');

                if (input.type === "password") {
                    input.type = "text"; // Ubah jadi teks biasa
                    eyeOpen.classList.add('hidden'); // Sembunyikan mata terbuka
                    eyeClosed.classList.remove('hidden'); // Tampilkan mata tertutup (dicoret)
                } else {
                    input.type = "password"; // Balikin jadi password (titik-titik)
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            }
        </script>
</body>
</html>