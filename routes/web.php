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

    // 5. Profil Saya 
    Route::get('/profile', [SnapController::class, 'profile'])->name('profile');

    // 6. ALUR UPLOAD BARU (UPLOAD -> EDITOR -> SAVE)
    
    // A. Halaman Pilih Frame & Upload Foto
    Route::get('/upload', [SnapController::class, 'create'])->name('upload.create');
    
    // B. Proses Foto Mentah & Masuk ke Halaman Editor Canvas
    // (Menggantikan route 'store' yang lama)
    Route::post('/editor', [SnapController::class, 'showEditor'])->name('upload.editor');
    
    // C. Simpan Hasil Akhir dari Canvas (AJAX)
    Route::post('/save-result', [SnapController::class, 'saveResult'])->name('upload.save-result');

});