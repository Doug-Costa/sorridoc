<?php

/**
 * SorriDoc Deploy Helper
 * Utilitário para execução de comandos Artisan em hospedagem compartilhada.
 */

// SEGURANÇA: Defina uma senha simples ou remova este arquivo após o uso!
$token = 'sorridoc_deploy_2024'; 

if (!isset($_GET['token']) || $_GET['token'] !== $token) {
    die('Acesso não autorizado. Use ?token=' . $token);
}

// Caminho para o autoload do Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

use Illuminate\Support\Facades\Artisan;

/**
 * Executa o comando e captura o feedback de forma segura
 */
function run($command) {
    echo "<h3>Executando: php artisan $command</h3>";
    try {
        $status = Artisan::call($command);
        echo "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc; max-height: 400px; overflow: auto;'>";
        echo Artisan::output();
        echo "</pre>";
        echo "<p>Status de saída: <b>$status</b></p>";
        return $status;
    } catch (\Throwable $e) {
        echo "<div style='background:#fee; color:#a00; padding:10px; border:1px solid #a00;'>";
        echo "<b>Erro ao executar comando:</b> " . $e->getMessage();
        echo "</div>";
        return 1;
    }
}

$action = $_GET['action'] ?? 'status';

echo "<h1>SorriDoc Deploy Helper</h1>";
echo "<nav style='background:#eee; padding: 10px; border-radius: 5px;'>
    <a href='?token=$token&action=status'>[ Status ]</a> | 
    <a href='?token=$token&action=migrate'>[ Rodar Migrações ]</a> | 
    <a href='?token=$token&action=clear'>[ Limpar Cache ]</a> | 
    <a href='?token=$token&action=optimize'>[ Otimizar ]</a> |
    <a href='?token=$token&action=storage_link'>[ Criar Storage Link ]</a>
</nav><hr>";

switch ($action) {
    case 'migrate':
        run('migrate --force');
        break;

    case 'clear':
        run('cache:clear');
        run('config:clear');
        run('route:clear');
        run('view:clear');
        break;

    case 'optimize':
        run('config:cache');
        run('route:cache');
        run('view:cache');
        break;

    case 'storage_link':
        echo "<h3>Tentando criar link de storage...</h3>";
        $target = storage_path('app/public');
        $link = public_path('storage');
        
        if (file_exists($link)) {
            echo "<p style='color:orange'>O link 'storage' já existe na pasta public.</p>";
        } else {
            // Tentativa via PHP nativo (muito mais chance de funcionar em compartilhada)
            if (@symlink($target, $link)) {
                echo "<p style='color:green'>Link criado com sucesso via PHP symlink()!</p>";
            } else {
                echo "<p style='color:red'>Falha ao criar via PHP. Tentando via Artisan...</p>";
                run('storage:link');
            }
        }
        break;

    case 'status':
        echo "<p>Sistema pronto. Escolha uma ação acima para começar.</p>";
        echo "<b>Ambiente:</b> " . app()->environment() . "<br>";
        echo "<b>Versão Laravel:</b> " . app()->version();
        break;

    default:
        echo "Ação desconhecida.";
        break;
}
