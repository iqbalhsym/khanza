<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIMRS - Web Routes
|--------------------------------------------------------------------------
*/

// Root → Dashboard
Route::get('/', fn() => redirect('/dashboard'));

/*
|--------------------------------------------------------------------------
| Application Routes (Protected Setup)
|--------------------------------------------------------------------------
*/

// Public Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/logout', function() {
    Session::flush();
    return redirect('/login');
});

Route::get('/captcha', [App\Http\Controllers\AuthController::class, 'generateCaptcha']);

// Protected Internal Routes (Must be logged in via LDAP/Lokal)
Route::middleware('auth.session')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware('role:dashboard');

    // Pendaftaran
    Route::prefix('pendaftaran')->middleware('role:pendaftaran')->group(function () {
        Route::get('/',            [App\Http\Controllers\PasienController::class, 'index']);
        Route::get('/pasien-baru', [App\Http\Controllers\PasienController::class, 'create']);
        Route::get('/pasien-lama', [App\Http\Controllers\PasienController::class, 'index']);
        Route::post('/store',      [App\Http\Controllers\PasienController::class, 'store']);
        Route::get('/edit/{id}',   [App\Http\Controllers\PasienController::class, 'edit']);
        Route::put('/update/{id}', [App\Http\Controllers\PasienController::class, 'update']);
        Route::delete('/delete/{id}', [App\Http\Controllers\PasienController::class, 'destroy']);
    });

    // Rawat Jalan
    Route::prefix('rawat-jalan')->middleware('role:rawat_jalan')->group(function () {
        Route::get('/', [App\Http\Controllers\RawatJalanController::class, 'index']);
        Route::get('/daftar', [App\Http\Controllers\RawatJalanController::class, 'create']);
        Route::post('/store', [App\Http\Controllers\RawatJalanController::class, 'store']);
        Route::get('/pemeriksaan', fn() => redirect('/rawat-jalan')); // Redirect if no parameter
        Route::get('/registered/{no_rawat}', [App\Http\Controllers\RawatJalanController::class, 'showRegisteredDetail'])->where('no_rawat', '.*');
        Route::post('/registered/store-dpjp', [App\Http\Controllers\RawatJalanController::class, 'storeDpjpTambahan']);
        Route::post('/registered/store-identification', [App\Http\Controllers\RawatJalanController::class, 'storeIdentification']);
        Route::delete('/registered/delete-identification/{id}', [App\Http\Controllers\RawatJalanController::class, 'deleteIdentification']);
        Route::post('/registered/store-allergy', [App\Http\Controllers\RawatJalanController::class, 'storeAllergy']);
        Route::delete('/registered/delete-allergy/{id}', [App\Http\Controllers\RawatJalanController::class, 'deleteAllergy']);
        Route::get('/pemeriksaan/{no_rawat}', [App\Http\Controllers\PemeriksaanController::class, 'create'])->where('no_rawat', '.*');
        Route::post('/pemeriksaan/store', [App\Http\Controllers\PemeriksaanController::class, 'store']);
        Route::get('/resep/{no_rawat}', [App\Http\Controllers\ResepController::class, 'create'])->where('no_rawat', '.*');
        Route::post('/resep/store', [App\Http\Controllers\ResepController::class, 'store']);
        Route::get('/search-obat', [App\Http\Controllers\ResepController::class, 'searchObat']);
        Route::get('/search-pasien', [App\Http\Controllers\RawatJalanController::class, 'searchPasien']);
    });

    // Farmasi
    Route::prefix('farmasi')->middleware('role:farmasi')->group(function () {
        Route::get('/', [App\Http\Controllers\ResepController::class, 'index']);
        Route::get('/stok', [App\Http\Controllers\FarmasiController::class, 'stok']);
        Route::post('/dispense/{no_resep}', [App\Http\Controllers\ResepController::class, 'dispense']);
    });

    // Billing / Kasir
    Route::prefix('billing')->middleware('role:billing')->group(function () {
        Route::get('/', [App\Http\Controllers\BillingController::class, 'index']);
        Route::get('/show/{no_rawat}', [App\Http\Controllers\BillingController::class, 'show'])->where('no_rawat', '.*');
        Route::post('/pay', [App\Http\Controllers\BillingController::class, 'pay']);
    });

    // Rawat Inap
    Route::prefix('rawat-inap')->middleware('role:rawat_inap')->group(function () {
        Route::get('/', [App\Http\Controllers\RawatInapController::class, 'index']);
        Route::get('/kamar', [App\Http\Controllers\RawatInapController::class, 'kamar']);
    });

    // Laboratorium
    Route::prefix('laboratorium')->middleware('role:laboratorium')->group(function () {
        Route::get('/',            [App\Http\Controllers\LaboratoriumController::class, 'index']);
        Route::get('/request/{no_rawat}', [App\Http\Controllers\LaboratoriumController::class, 'createRequest'])->where('no_rawat', '.*');
        Route::post('/request/store', [App\Http\Controllers\LaboratoriumController::class, 'storeRequest']);
        Route::get('/input/{noorder}', [App\Http\Controllers\LaboratoriumController::class, 'input']);
        Route::post('/store',       [App\Http\Controllers\LaboratoriumController::class, 'store']);
        Route::get('/hasil',       [App\Http\Controllers\LaboratoriumController::class, 'hasil']);
        Route::get('/view-hasil/{no_rawat}/{tgl}/{jam}', [App\Http\Controllers\LaboratoriumController::class, 'showResult'])->where('no_rawat', '.*');
    });

    // Radiologi
    Route::prefix('radiologi')->middleware('role:radiologi')->group(function () {
        Route::get('/', [App\Http\Controllers\RadiologiController::class, 'index']);
        Route::get('/request/{no_rawat}', [App\Http\Controllers\RadiologiController::class, 'createRequest'])->where('no_rawat', '.*');
        Route::post('/request/store', [App\Http\Controllers\RadiologiController::class, 'storeRequest']);
        Route::get('/periksa/{noorder}', [App\Http\Controllers\RadiologiController::class, 'pemeriksaan']);
        Route::post('/periksa/store', [App\Http\Controllers\RadiologiController::class, 'storePemeriksaan']);
        Route::get('/input/{no_rawat}/{tgl}/{jam}', [App\Http\Controllers\RadiologiController::class, 'input'])->where('no_rawat', '.*');
        Route::post('/store', [App\Http\Controllers\RadiologiController::class, 'store']);
    });

    // Master Data
    Route::prefix('master')->middleware('role:master_data')->group(function () {
        Route::get('/',        [App\Http\Controllers\MasterController::class, 'pasien']); // Default to pasien
        Route::get('/pasien',  [App\Http\Controllers\MasterController::class, 'pasien']);
        Route::get('/dokter',  [App\Http\Controllers\MasterController::class, 'dokter']);
        Route::post('/dokter/toggle-status/{kd_dokter}', [App\Http\Controllers\MasterController::class, 'toggleStatus']);
        Route::get('/poli',    [App\Http\Controllers\MasterController::class, 'poli']);
        Route::get('/obat',    [App\Http\Controllers\MasterController::class, 'obat']);
        Route::get('/kamar',   [App\Http\Controllers\MasterController::class, 'kamar']);
        Route::get('/tarif',   [App\Http\Controllers\MasterController::class, 'tarif']);
        Route::get('/aset',    [App\Http\Controllers\MasterController::class, 'aset']);
    });

    // Laporan
    Route::get('/laporan', fn() => view('laporan.index'))->middleware('role:laporan');

    // Pengaturan & RBAC
    Route::middleware('role:pengaturan')->group(function () {
        Route::get('/pengaturan', [App\Http\Controllers\PengaturanController::class, 'index']);
        Route::post('/pengaturan/users', [App\Http\Controllers\PengaturanController::class, 'storeUser']);
        Route::put('/pengaturan/users/{id}', [App\Http\Controllers\PengaturanController::class, 'updateUser']);
        Route::delete('/pengaturan/users/{id}', [App\Http\Controllers\PengaturanController::class, 'destroyUser']);
        Route::put('/pengaturan/roles/{id}', [App\Http\Controllers\PengaturanController::class, 'updateRolePermissions']);
    });

    // Legacy Pasien route (kept for compatibility)
    Route::get('/pasien',         [App\Http\Controllers\PasienController::class, 'index'])->middleware('role:pendaftaran');
    Route::get('/pasien/create',  fn() => view('pendaftaran.pasien_baru'))->middleware('role:pendaftaran');
    Route::post('/pasien/store',  [App\Http\Controllers\PasienController::class, 'store'])->middleware('role:pendaftaran');
});
