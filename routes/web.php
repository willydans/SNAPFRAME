<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SnapController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses TANPA login)
|--------------------------------------------------------------------------
*/

// 1. Landing Page (Halaman Depan yang ada animasinya)
Route::get('/', function () {
    return view('welcome');
})->name('landing');

// Route Auth (Login/Register/Logout)
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
    
    // 2. Dashboard (Pusat Kontrol & Statistik)
    // Pastikan di Controller function index() me-return view('dashboard')
    Route::get('/dashboard', [SnapController::class, 'index'])->name('dashboard');
    
    // 3. Galeri Frame (Katalog Pilihan Frame)
    Route::get('/frames', [SnapController::class, 'gallery'])->name('gallery');

    // 4. Pekerjaan Saya (History Projek) - Jalur Baru!
    // Pastikan di Controller ada function pekerjaan()
    Route::get('/pekerjaan-saya', [SnapController::class, 'pekerjaan'])->name('pekerjaan');

    // 5. Profil Saya - Jalur Baru!
    // Pastikan di Controller ada function profile()
    Route::get('/profile', [SnapController::class, 'profile'])->name('profile');

    // 6. Upload Foto (Buat Projek Baru)
    Route::get('/upload', [SnapController::class, 'create'])->name('upload.create');
    Route::post('/upload', [SnapController::class, 'store'])->name('upload.store');

});