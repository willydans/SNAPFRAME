<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SnapFrame OCI - Cloud Creative Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Efek Kaca */
        .glass { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        
        /* Animasi Blob Background */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</head>
<body class="antialiased bg-gray-900 text-white overflow-x-hidden">

    <div class="fixed inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-pink-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative z-10 flex flex-col min-h-screen">
        
        <nav class="flex justify-between items-center px-8 py-6 max-w-7xl mx-auto w-full">
            <div class="text-2xl font-bold tracking-tighter cursor-default">
                Snap<span class="text-blue-500">Frame</span>.
            </div>
            
            </nav>

        <main class="flex-grow flex items-center justify-center px-8">
            <div class="max-w-7xl mx-auto w-full flex flex-col lg:flex-row items-center justify-between">
                
                <div class="lg:w-1/2 space-y-8 text-center lg:text-left">
                    <h1 class="text-6xl lg:text-7xl font-extrabold leading-tight">
                        Bingkai Momen <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-600">Tanpa Batas.</span>
                    </h1>
                    <p class="text-gray-400 text-lg max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Platform cloud native untuk menggabungkan, mengedit, dan menyimpan kenangan Anda dengan teknologi pemrosesan gambar tercepat.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 rounded-xl font-bold text-lg transition shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2">
                            <span>Mulai Gratis</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        
                        </div>
                </div>

                <div class="lg:w-1/2 mt-12 lg:mt-0 relative hidden lg:block">
                    <div class="relative w-full aspect-square max-w-md mx-auto">
                        <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="absolute top-0 right-0 w-64 h-80 object-cover rounded-2xl shadow-2xl transform rotate-6 border-4 border-gray-800 z-10 hover:rotate-0 transition duration-500">
                        
                        <img src="https://images.unsplash.com/photo-1554080353-a576cf803bda?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="absolute top-10 left-10 w-60 h-72 object-cover rounded-2xl shadow-2xl transform -rotate-12 border-4 border-gray-800 hover:-rotate-0 transition duration-500">
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="py-6 text-center text-gray-600 text-sm">
            &copy; 2025 SnapFrame OCI Project.
        </footer>
    </div>
</body>
</html>