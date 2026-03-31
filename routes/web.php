<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprovalController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/approvals/download/{approval}', [ApprovalController::class, 'downloadCertificate'])->name('approvals.download');
});

// Rota de verificação pública
Route::get('/v/{hash}', [ApprovalController::class, 'verify'])->name('approvals.verify');
