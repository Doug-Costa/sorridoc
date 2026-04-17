<?php

use App\Http\Controllers\RhPortalController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WorkerPortalController;
use Illuminate\Support\Facades\Route;

Route::prefix('rh/{token}')->group(function () {
    Route::get('/', [RhPortalController::class, 'showLogin'])->name('rh.login');
    Route::post('/auth', [RhPortalController::class, 'authenticate'])->name('rh.auth');

    Route::middleware(['rh.session'])->group(function () {
        Route::get('/dashboard', [RhPortalController::class, 'dashboard'])->name('rh.dashboard');
        Route::get('/trabalhadores', [RhPortalController::class, 'workers'])->name('rh.workers');
        Route::get('/trabalhador/{worker}', [RhPortalController::class, 'showWorker'])->name('rh.worker.show');
        Route::post('/logout', [RhPortalController::class, 'logout'])->name('rh.logout');
    });
});

Route::prefix('worker')->group(function () {
    Route::get('/', [WorkerPortalController::class, 'showLogin'])->name('worker.login');
    Route::post('/auth', [WorkerPortalController::class, 'authenticate'])->name('worker.auth');
    Route::get('/verificar-token', [WorkerPortalController::class, 'showTokenVerification'])->name('worker.verify-token');
    Route::post('/verificar-token', [WorkerPortalController::class, 'verifyToken']);

    Route::middleware(['worker.session'])->group(function () {
        Route::get('/dashboard', [WorkerPortalController::class, 'dashboard'])->name('worker.dashboard');
        Route::get('/documento/{document}/download', [WorkerPortalController::class, 'download'])->name('worker.document.download');
        Route::post('/logout', [WorkerPortalController::class, 'logout'])->name('worker.logout');
    });
});

Route::prefix('upload/{token}')->group(function () {
    Route::get('/', [UploadController::class, 'showLogin'])->name('upload.login');
    Route::post('/auth', [UploadController::class, 'authenticate'])->name('upload.auth');

    Route::middleware(['upload.session'])->group(function () {
        Route::get('/index', [UploadController::class, 'index'])->name('upload.index');
        Route::post('/store', [UploadController::class, 'store'])->name('upload.store');
        Route::post('/logout', [UploadController::class, 'logout'])->name('upload.logout');
    });
});
