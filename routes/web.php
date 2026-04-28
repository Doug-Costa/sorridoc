<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprovalController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/approvals/download/{approval}', [ApprovalController::class, 'downloadCertificate'])->name('approvals.download');
    Route::get('/approvals/view-document/{approval}', [ApprovalController::class, 'viewDocument'])->name('approvals.view-document');
});

// Rota de verificação pública
Route::get('/v/{hash}', [ApprovalController::class, 'verify'])->name('approvals.verify');

// ROTAS TEMPORÁRIAS PARA DEPLOY (Remova ou comente após usar)
use Illuminate\Support\Facades\Artisan;

Route::get('/deploy-setup', function () {
    try {
        $output = '';
        
        Artisan::call('optimize:clear');
        $output .= "Limpeza de Cache:\n" . Artisan::output() . "\n\n";
        
        Artisan::call('migrate', ['--force' => true]);
        $output .= "Migrações (Banco de Dados):\n" . Artisan::output() . "\n\n";
        
        Artisan::call('storage:link');
        $output .= "Link de Storage:\n" . Artisan::output() . "\n\n";
        
        return "<h3>Deploy Setup Executado com Sucesso!</h3><pre>{$output}</pre>";
    } catch (\Exception $e) {
        return "<h3>Erro ao executar setup:</h3><pre>" . $e->getMessage() . "</pre>";
    }
});

Route::get('/create-admin', function () {
    try {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@sorridoc.com.br'],
            [
                'name' => 'Admin SorriDoc',
                'password' => bcrypt('admin123'),
                // Se a tabela tiver a coluna role, descomente abaixo
                // 'role' => 'Super Admin', 
            ]
        );
        return "<h3>Admin criado com sucesso!</h3><p>E-mail: <b>{$user->email}</b></p><p>Senha: <b>admin123</b></p>";
    } catch (\Exception $e) {
        return "Erro: " . $e->getMessage();
    }
});
