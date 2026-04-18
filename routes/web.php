<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PortalAuthController;
use App\Http\Controllers\PortalController;

Route::get('/', function () {
    return redirect('/admin');
});

// Fallback login route for system compatibility
Route::get('/login', fn() => redirect()->route('portal.login'))->name('login');

// Portal SorriMed Routes
Route::prefix('portal')->name('portal.')->group(function () {
    // Auth
    Route::get('/login', [PortalAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [PortalAuthController::class, 'login']);
    Route::post('/logout', [PortalAuthController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware(['portal.access'])->group(function () {
        Route::get('/', function () {
            $user = Auth::user();
            return $user->role === 'Empresa' ? redirect()->route('portal.company.dashboard') : redirect()->route('portal.worker.dashboard');
        });

        Route::get('/empresa', [PortalController::class, 'companyDashboard'])->name('company.dashboard');
        Route::get('/funcionario', [PortalController::class, 'workerDashboard'])->name('worker.dashboard');
        Route::get('/download/{document}', [PortalController::class, 'downloadDocument'])->name('download');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/approvals/download/{approval}', [ApprovalController::class, 'downloadCertificate'])->name('approvals.download');
});

// Atestado de verificação pública
Route::get('/v/{hash}', [ApprovalController::class, 'verify'])->name('approvals.verify');
