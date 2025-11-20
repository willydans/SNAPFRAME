<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SnapController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib Login)
|--------------------------------------------------------------------------
| Group middleware 'auth' akan menendang user ke halaman login
| jika mereka belum masuk.
*/
Route::middleware('auth')->group(function () {
    
    // Halaman Default setelah login (Galeri Frame)
    Route::get('/', [SnapController::class, 'gallery'])->name('home'); 
    Route::get('/frames', [SnapController::class, 'gallery'])->name('gallery');

    // Dashboard Pekerjaan Saya
    Route::get('/dashboard', [SnapController::class, 'index'])->name('dashboard');

    // Upload
    Route::get('/upload', [SnapController::class, 'create'])->name('upload.create');
    Route::post('/upload', [SnapController::class, 'store'])->name('upload.store');

});