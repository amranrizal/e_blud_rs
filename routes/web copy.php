<?php

use Illuminate\Support\Facades\Route;

// --- IMPORT CONTROLLER ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\MasterProgramController;
use App\Http\Controllers\MasterRekeningController;
use App\Http\Controllers\MasterRefController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\DashboardController;
use App\Models\Program;
use App\Models\Rekening;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| Web Routes (VERSI FINAL & AMAN)
|--------------------------------------------------------------------------
*/

// --- 1. JALUR TAMU (GUEST) ---
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// --- 2. JALUR KHUSUS USER LOGIN (AUTH) ---
Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    
    // Dashboard (Sesuaikan jika Anda punya controller sendiri)
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified']) // Sesuaikan middleware yg ada
    ->name('dashboard');
    
    //Route::get('/dashboard', function () { 
        // 1. Hitung Data (Query Ringan)
     //  $total_program  = Program::count();
     //  $total_rekening = Rekening::count();
     //  $total_unit     = \App\Models\UnitKerja::count(); // Jika ada model UnitKerja
        
        // 2. Kirim ke View
     //  return view('dashboard', compact('total_program', 'total_rekening', 'total_unit'));
    // })->name('dashboard');

    // Manajemen User
    Route::resource('users', UserController::class);


    // --- GROUP MASTER DATA ---
    Route::prefix('master')->name('master.')->group(function () {
        
        // A. MASTER PROGRAM (KOMBINASI)
        // 1. Rute Simpan Khusus (Sesuai SS Anda agar 'Tambah' tidak error)
        Route::post('/program/store', [MasterProgramController::class, 'storeProgram'])->name('program.store');
        Route::post('/kegiatan/store', [MasterProgramController::class, 'storeKegiatan'])->name('kegiatan.store');
        Route::post('/sub-kegiatan/store', [MasterProgramController::class, 'storeSubKegiatan'])->name('sub_kegiatan.store');
        
        // 2. Rute Standar (Agar 'Edit', 'Update', 'Delete' BISA JALAN)
        // PENTING: Saya hapus 'except' agar Edit & Update aktif
        Route::resource('program', MasterProgramController::class)->except(['store']); 


        // B. MASTER REKENING
        Route::resource('rekening', MasterRekeningController::class);

        // C. MASTER UNIT
        Route::resource('unit', UnitController::class);

        // D. REFERENSI
        Route::get('referensi', [MasterRefController::class, 'index'])->name('ref.index');
    });


    // --- TRANSAKSI PENGANGGARAN ---
    Route::resource('anggaran', AnggaranController::class);
    Route::post('anggaran/{id}/ajukan', [AnggaranController::class, 'ajukan'])->name('anggaran.ajukan');

    // --- VERIFIKASI ---
    Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [AnggaranController::class, 'verifikasiIndex'])->name('index');
        Route::post('/approve/{id}', [AnggaranController::class, 'approve'])->name('approve');
        Route::post('/reject/{id}', [AnggaranController::class, 'reject'])->name('reject');
    });

});