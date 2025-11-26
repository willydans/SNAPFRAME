<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SnapController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses TANPA login)
|--------------------------------------------------------------------------
*/

// 1. Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// Route Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (WAJIB Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // 2. Dashboard (Pusat Kontrol)
    Route::get('/dashboard', [SnapController::class, 'index'])->name('dashboard');
    
    // 3. Galeri Frame
    Route::get('/frames', [SnapController::class, 'gallery'])->name('gallery');

    // 4. Pekerjaan Saya (History Projek)
    // Kita arahkan ke index() karena isinya sama (list pekerjaan)
    Route::get('/pekerjaan-saya', [SnapController::class, 'index'])->name('pekerjaan');

    // 5. Profil Saya (Opsional - Jika belum ada method profile, arahkan ke dashboard dulu atau buat method kosong)
    // Route::get('/profile', [SnapController::class, 'profile'])->name('profile');

    // 6. Upload Foto (Buat Projek Baru)
    // PENTING: Nama route ini HARUS 'upload.create' agar sesuai dengan layout.blade.php dan tombol di galeri
    Route::get('/upload', [SnapController::class, 'create'])->name('upload.create');
    Route::post('/upload', [SnapController::class, 'store'])->name('upload.store');

    // 7. Request Frame (Opsional)
    // Route::get('/request-frame', [SnapController::class, 'requestFrame'])->name('gallery.request');

});