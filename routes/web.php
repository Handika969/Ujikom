<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/member/login', fn () => redirect()->route('login'))->name('member.login');
Route::post('/member/login', fn () => redirect()->route('login'))->name('member.login.submit');
Route::get('/member/register', [MemberAuthController::class, 'showRegister'])->name('member.register');
Route::post('/member/register', [MemberAuthController::class, 'register'])->name('member.register.submit');
Route::get('/member/check-username', [MemberAuthController::class, 'checkUsername'])->name('member.check-username');
Route::post('/member/logout', [MemberAuthController::class, 'logout'])->name('member.logout');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

        Route::get('/admin/tarif', [AdminController::class, 'tarifs'])->name('admin.tarif');
        Route::post('/admin/tarif', [AdminController::class, 'storeTarif'])->name('admin.tarif.store');
        Route::put('/admin/tarif/{tarif}', [AdminController::class, 'updateTarif'])->name('admin.tarif.update');
        Route::delete('/admin/tarif/{tarif}', [AdminController::class, 'deleteTarif'])->name('admin.tarif.delete');

        Route::get('/admin/area', [AdminController::class, 'areas'])->name('admin.area');
        Route::post('/admin/area', [AdminController::class, 'storeArea'])->name('admin.area.store');
        Route::put('/admin/area/{area}', [AdminController::class, 'updateArea'])->name('admin.area.update');
        Route::delete('/admin/area/{area}', [AdminController::class, 'deleteArea'])->name('admin.area.delete');

        Route::get('/admin/kendaraan', [AdminController::class, 'kendaraan'])->name('admin.kendaraan');
        Route::post('/admin/kendaraan', [AdminController::class, 'storeKendaraan'])->name('admin.kendaraan.store');
        Route::put('/admin/kendaraan/{kendaraan}', [AdminController::class, 'updateKendaraan'])->name('admin.kendaraan.update');
        Route::delete('/admin/kendaraan/{kendaraan}', [AdminController::class, 'deleteKendaraan'])->name('admin.kendaraan.delete');

        Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
    });

    Route::middleware(['role:petugas'])->group(function () {
        Route::get('/petugas/transaksi', [PetugasController::class, 'dashboard'])->name('petugas.dashboard');
        Route::get('/petugas/masuk', [PetugasController::class, 'createEntry'])->name('petugas.entry');
        Route::post('/petugas/masuk', [PetugasController::class, 'storeEntry'])->name('petugas.store-entry');
        Route::get('/petugas/check-member', [PetugasController::class, 'checkMember'])->name('petugas.check-member');
        Route::get('/petugas/scan-member-qr', [PetugasController::class, 'scanMemberQr'])->name('petugas.scan-member-qr');
        Route::get('/petugas/member-link', [PetugasController::class, 'memberLinkForm'])->name('petugas.member-link-form');
        Route::post('/petugas/member-link', [PetugasController::class, 'saveMemberLink'])->name('petugas.member-link-save');
        Route::get('/petugas/keluar/{id}', [PetugasController::class, 'checkout'])->name('petugas.checkout');
        Route::post('/petugas/keluar/{id}', [PetugasController::class, 'processExit'])->name('petugas.process-exit');
        Route::get('/petugas/tiket-hilang/{id}', [PetugasController::class, 'lostTicketForm'])->name('petugas.lost-ticket');
        Route::post('/petugas/tiket-hilang/{id}', [PetugasController::class, 'processLostTicket'])->name('petugas.process-lost-ticket');
        Route::get('/petugas/cetak/{id}', [PetugasController::class, 'printReceipt'])->name('petugas.print');
        Route::get('/petugas/riwayat', [PetugasController::class, 'history'])->name('petugas.history');
        Route::get('/petugas/topup-pending', [PetugasController::class, 'pendingTopup'])->name('petugas.topup-pending');
        Route::get('/petugas/topup-proof/{topup}', [PetugasController::class, 'showTopupProof'])->name('petugas.topup-proof');
        Route::post('/petugas/topup-verify/{topup}', [PetugasController::class, 'verifyTopup'])->name('petugas.topup-verify');
    });

    Route::middleware(['role:owner'])->group(function () {
        Route::get('/owner/laporan', [OwnerController::class, 'index'])->name('owner.dashboard');
        Route::get('/owner/laporan/print', [OwnerController::class, 'print'])->name('owner.print');
    });
});

Route::middleware(['member.session'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/topup', [MemberPortalController::class, 'topupForm'])->name('topup.form');
    Route::post('/topup', [MemberPortalController::class, 'submitTopup'])->name('topup.submit');
    Route::get('/topup-history', [MemberPortalController::class, 'topupHistory'])->name('topup.history');
    Route::get('/notifications', [MemberPortalController::class, 'notifications'])->name('notifications');
    Route::post('/kendaraan', [MemberPortalController::class, 'addKendaraan'])->name('kendaraan.store');
    Route::delete('/kendaraan/{kendaraan}', [MemberPortalController::class, 'deleteKendaraan'])->name('kendaraan.delete');
});
