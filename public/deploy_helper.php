<?php

/**
 * SorriDoc Deploy Helper
 * Este script auxilia na sincronização do ambiente em hospedagens compartilhadas.
 * 
 * INSTRUÇÕES:
 * 1. Envie este arquivo para a pasta 'public' do seu servidor.
 * 2. Acesse seu-dominio.com/deploy_helper.php
 * 3. Após a execução bem-sucedida, EXCLUA este arquivo do servidor por segurança.
 */

use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Tenta carregar o autoloader do Laravel
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$appPath = __DIR__ . '/../bootstrap/app.php';

if (!file_exists($autoloadPath) || !file_exists($appPath)) {
    die("Erro: Não foi possível localizar o diretório 'vendor' ou 'bootstrap'. Certifique-se de que o projeto Laravel foi enviado corretamente.");
}

require $autoloadPath;
$app = require_once $appPath;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<html><head><title>SorriDoc Deploy Helper</title>";
echo "<style>body { font-family: sans-serif; line-height: 1.6; padding: 20px; background: #f4f4f9; color: #333; } 
      .container { max-width: 800px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
      h1 { color: #4f46e5; border-bottom: 2px solid #eef2ff; padding-bottom: 10px; }
      .success { color: #059669; font-weight: bold; }
      .error { color: #dc2626; font-weight: bold; }
      pre { background: #1e293b; color: #f8fafc; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 14px; }</style>";
echo "</head><body><div class='container'>";
echo "<h1>🚀 SorriDoc Deploy Helper</h1>";

function runCommand($command, $description) {
    echo "<h3>> {$description}...</h3>";
    try {
        Artisan::call($command);
        echo "<pre>" . Artisan::output() . "</pre>";
        echo "<p class='success'>✓ Concluído com sucesso.</p>";
    } catch (\Exception $e) {
        echo "<p class='error'>✗ Erro ao executar: " . $e->getMessage() . "</p>";
    }
}

// 1. Migrações
runCommand('migrate --force', 'Executando migrações do banco de dados');

// 2. Limpeza de Caches
runCommand('optimize:clear', 'Limpando e otimizando caches (config, route, view)');

// 3. Correção de Acesso do Administrador
echo "<h3>> Verificando permissões de administrador...</h3>";
try {
    $email = 'admin@sorridoc.com.br';
    $user = User::where('email', $email)->first();

    if ($user) {
        $user->role = 'Super Admin';
        $user->save();
        echo "<p class='success'>✓ Usuário '{$email}' agora possui o papel 'Super Admin'.</p>";
    } else {
        echo "<p class='error'>⚠ Usuário '{$email}' não encontrado no banco de dados.</p>";
        
        // Opcional: Criar o usuário se ele não existir
        /*
        User::create([
            'name' => 'Administrador',
            'email' => $email,
            'password' => bcrypt('MUDAR_SENHA_AQUI'),
            'role' => 'Super Admin',
        ]);
        echo "<p class='success'>✓ Usuário '{$email}' foi criado com sucesso.</p>";
        */
    }
} catch (\Exception $e) {
    echo "<p class='error'>✗ Erro ao atualizar usuário: " . $e->getMessage() . "</p>";
}

echo "<hr><p style='color: #ef4444; font-weight: bold; text-align: center;'>⚠️ IMPORTANTE: EXCLUA ESTE ARQUIVO DO SERVIDOR AGORA! ⚠️</p>";
echo "</div></body></html>";

$kernel->terminate($request, $response);
