<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// =========================
// IMPORT CONTROLLER
// =========================
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\MasterProgramController;
use App\Http\Controllers\MasterRekeningController;
use App\Http\Controllers\MasterRefController;
use App\Http\Controllers\StandarHargaController;
use App\Http\Controllers\PaguIndikatifController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\RenjaController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\CetakRkaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\LaporanRbaController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\HspkController;
use App\Http\Controllers\AsbController;
use App\Exports\SshExport;




// =========================
// GUEST (BELUM LOGIN)
// =========================

Route::get('/ajax/sbu-parameter/{id}', function ($id) {

    $sbu = \App\Models\StandarHarga::with('sbuParameters')
        ->where('kode_kelompok', 'SBU')
        ->findOrFail($id);

    return $sbu->sbuParameters->map(function ($p) {
        return [
            'kode' => $p->kode_parameter,
            'label' => $p->label,
            'tipe' => $p->tipe,
            'default' => $p->nilai_default,
            'required' => $p->is_required,
        ];
    });

})->name('ajax.sbu-parameter');

Route::get('/generate-ssh', function () {

    $data = json_decode(file_get_contents(storage_path('app/ssh_full_2026.json')), true);

    $formatted = collect($data)->map(function ($item) {
        return [
            $item['kode'] ?? '',
            $item['uraian'] ?? '',
            $item['spesifikasi'] ?? '',
            $item['satuan'] ?? '',
            $item['harga'] ?? 0,
            $item['tkdn'] ?? 0,
        ];
    })->toArray();

    Excel::store(new SshExport($formatted), 'public/SSH_2026.xlsx');

    return "DONE";
});

Route::get('/ajax/rekening-belanja', function (\Illuminate\Http\Request $request) {

    $q = $request->get('q');

    return \App\Models\Rekening::where('level', 'Sub Rincian Objek')
        ->when($q, function ($query) use ($q) {
            $query->where('kode_akun', 'like', "%$q%")
                  ->orWhere('nama_akun', 'like', "%$q%");
        })
        ->limit(20)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->kode_akun . ' - ' . $item->nama_akun,
            ];
        });

})->name('ajax.rekening-belanja');

Route::get('/rekening/{id}', function ($id) {
    $rek = \App\Models\Rekening::find($id);

    if (!$rek) {
        return response()->json(null, 404);
    }

    return response()->json([
        'id' => $rek->id,
        'text' => $rek->kode_akun . ' - ' . $rek->nama_akun
    ]);
})->name('rekening.byId');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::put('/indikator-program/{id}', 
    [App\Http\Controllers\IndikatorProgramController::class, 'update']
)->name('indikator.program.update');

Route::delete('/indikator-program/{id}', 
    [App\Http\Controllers\IndikatorProgramController::class, 'destroy']
)->name('indikator.program.destroy');


// =========================
// AUTH (SEMUA ROLE)
// =========================
Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cetak-rka/{id}', [CetakRkaController::class, 'printRKA'])->name('cetak.rka');

});

// =========================
// ADMIN SAJA (FULL AKSES)
// =========================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // =========================
    // PENDAPATAN
    // =========================
    Route::prefix('pendapatan')->name('pendapatan.')->group(function () {
        Route::get('/', [PendapatanController::class, 'index'])->name('index');
        Route::get('/create', [PendapatanController::class, 'create'])->name('create');
        Route::post('/store', [PendapatanController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PendapatanController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PendapatanController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PendapatanController::class, 'destroy'])->name('destroy');

    });

    // =========================
    // SETTINGS
    // =========================
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/instansi', [SettingController::class, 'updateInstansi'])->name('settings.instansi.update');
        Route::post('/pejabat', [SettingController::class, 'storePejabat'])->name('settings.pejabat.store');
        Route::put('/pejabat/{id}', [SettingController::class, 'updatePejabat'])->name('settings.pejabat.update');
        Route::delete('/pejabat/{id}', [SettingController::class, 'destroyPejabat'])->name('settings.pejabat.destroy');
    });

    Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {

        Route::post(
            '/rka/{budget}/force-status',
            [\App\Http\Controllers\Admin\AdminOverrideController::class, 'forceStatus']
        )->name('admin.rka.force-status');

    });

    // =========================
    // STANDAR HARGA (CRUD + IMPORT)
    // =========================
    Route::resource('standar-harga', StandarHargaController::class);
    Route::post('standar-harga/import', [StandarHargaController::class, 'import'])
        ->name('standar-harga.import');

    // =========================
    // RENJA / PAGU INDIKATIF
    // =========================
    Route::get('/renja', [RenjaController::class,'index'])->name('renja.index');
    Route::get('/renja/create', [RenjaController::class,'create'])->name('renja.create');
    Route::post('/renja/store', [RenjaController::class,'store'])->name('renja.store');
    Route::get('/renja/edit/{id}', [RenjaController::class,'edit'])->name('renja.edit');
    Route::put('/renja/update/{id}', [RenjaController::class,'update'])->name('renja.update');
    Route::delete('/renja/delete/{id}', [RenjaController::class,'destroy'])->name('renja.destroy');
    Route::get('/pagu-indikatif', [PaguIndikatifController::class,'index'])->name('pagu.index');

    // =========================
    // CETAK & APPROVAL
    // =========================

    
 //   Route::post('/approval/setujui/{id}', [ApprovalController::class, 'setujui'])->name('approval.setujui');
 //   Route::post('/approval/tolak/{id}', [ApprovalController::class, 'tolak'])->name('approval.tolak');
 //   Route::post('/approval/batal/{id}', [ApprovalController::class, 'batal'])->name('approval.batal');

    // =========================
    // MASTER DATA
    // =========================
    Route::prefix('master')->name('master.')->group(function () {

        Route::get('/program/search', [MasterProgramController::class, 'search'])->name('program.search');
        Route::get('/program/goto', [MasterProgramController::class, 'goto'])->name('program.goto');

        Route::resource('program', MasterProgramController::class);
        Route::post('/program/import', [MasterProgramController::class, 'import'])->name('program.import');
        Route::get('/program/goto', [MasterProgramController::class, 'goto'])->name('program.goto');
        
        Route::get('/rekening/search', [MasterRekeningController::class, 'search'])->name('rekening.search');
        Route::get('/rekening/goto', [MasterRekeningController::class, 'goto'])->name('rekening.goto');
        Route::resource('rekening', MasterRekeningController::class);
        Route::post('/rekening/import', [MasterRekeningController::class, 'import'])->name('rekening.import');

        Route::resource('unit', UnitController::class);
        Route::get('referensi', [MasterRefController::class, 'index'])->name('ref.index');
    });

    // =========================
    // USER MANAGEMENT
    // =========================
    Route::resource('users', UserController::class);
});

Route::post('/approval/ajukan/{id}', [ApprovalController::class, 'ajukan'])->name('approval.ajukan');
// =========================
// USER & BOSS (READONLY)
// =========================
Route::middleware(['auth', 'role:user,boss'])->group(function () {

    Route::get('/ssh', [StandarHargaController::class, 'readonly'])
        ->name('standar-harga.readonly');

});

// =========================
// RKA (SEMUA ROLE LOGIN)
// =========================
Route::prefix('rka')->middleware('auth')->group(function () {

    Route::get('/', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('/input/{id}', [BudgetController::class, 'create'])->name('budget.create');
    Route::post('/store', [BudgetController::class, 'store'])->name('budget.store');
    Route::put('/update/{id}', [BudgetController::class, 'update'])->name('budget.update');
    Route::delete('/delete/{id}', [BudgetController::class, 'destroy'])->name('budget.destroy');

    Route::post('/indikator/store', [IndikatorKinerjaController::class, 'store'])->name('indikator.store');
    Route::delete('/indikator/{id}', [IndikatorKinerjaController::class, 'destroy'])->name('indikator.destroy');
    Route::post('/indikator/update-rka', [IndikatorKinerjaController::class, 'updateFromRka'])->name('indikator.updateRka');
});

Route::post('/indikator-program/store', 
    [App\Http\Controllers\IndikatorProgramController::class, 'store']
)->name('indikator.program.store');


// =========================
// ANGGARAN & VERIFIKASI
// =========================
Route::middleware('auth')->group(function () {

    Route::get('laporan/rba', [LaporanRbaController::class, 'index'])->name('laporan.index');
    Route::get('laporan/rba/cetak', [LaporanRbaController::class, 'cetakRbaFull'])->name('laporan.cetakRbaFull');

    Route::resource('pembiayaan', App\Http\Controllers\PembiayaanController::class);
    Route::delete('/pembiayaan/{id}', [App\Http\Controllers\PembiayaanController::class, 'destroy'])->name('pembiayaan.destroy');
});

// routes/web.php
Route::middleware('auth')->group(function () {
    Route::post('/approval/setujui/{id}', [ApprovalController::class, 'setujui'])
        ->name('approval.setujui');

    Route::post('/approval/tolak/{id}', [ApprovalController::class, 'tolak'])
        ->name('approval.tolak');

    Route::post('/approval/batal/{id}', [ApprovalController::class, 'batal'])
        ->name('approval.batal');
     Route::post('/approval/batal-draft/{id}', [ApprovalController::class, 'batalDraft'])
        ->name('approval.batalkanDraft');

    Route::post('/approval/revisi-admin/{id}', [ApprovalController::class, 'revisiAdmin'])
        ->name('approval.revisiAdmin');

});

Route::prefix('hspk')->group(function () {

    // Boleh dilihat semua role
    Route::get('/', [HspkController::class, 'index'])->name('hspk.index');
    Route::get('/{id}', [HspkController::class, 'show'])->name('hspk.show');

    // Hanya admin & super admin
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::post('/', [HspkController::class, 'store'])->name('hspk.store');
        Route::post('/{id}/item', [HspkController::class, 'tambahItem'])->name('hspk.tambahItem');
        Route::delete('/item/{id}', [HspkController::class, 'hapusItem'])->name('hspk.hapusItem');
    });
});


Route::prefix('asb')->group(function () {
        Route::get('/', [AsbController::class, 'index'])->name('asb.index');
        Route::post('/', [AsbController::class, 'store'])->name('asb.store');
        Route::put('/{id}', [AsbController::class, 'update'])->name('asb.update');
        Route::delete('/{id}', [AsbController::class, 'destroy'])->name('asb.destroy');
});

Route::prefix('sbu')->group(function () {
    Route::get('/{id}', [StandarHargaController::class, 'showSbu'])
        ->name('sbu.show');

    Route::post('/{id}/parameter', [StandarHargaController::class, 'tambahParameter'])
        ->name('sbu.parameter.store');

    Route::delete('/parameter/{id}', [StandarHargaController::class, 'hapusParameter'])
        ->name('sbu.parameter.destroy');
});



