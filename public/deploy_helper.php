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

function run($command, $kernel) {
    echo "<h3>Executando: php artisan $command</h3>";
    $status = $kernel->call($command);
    echo "<pre>";
    echo Illuminate\Support\Facades\Artisan::output();
    echo "</pre>";
    echo "<hr>";
    return $status;
}

$action = $_GET['action'] ?? 'status';

echo "<h1>SorriDoc Deploy Helper</h1>";
echo "<nav>
    <a href='?token=$token&action=status'>[ Status ]</a> | 
    <a href='?token=$token&action=migrate'>[ Rodar Migrações ]</a> | 
    <a href='?token=$token&action=clear'>[ Limpar TUDO (Cache/Config/View) ]</a> | 
    <a href='?token=$token&action=optimize'>[ Otimizar (Cache Novo) ]</a> |
    <a href='?token=$token&action=storage_link'>[ Criar Storage Link ]</a>
</nav><hr>";

switch ($action) {
    case 'migrate':
        run('migrate --force', $kernel);
        break;

    case 'clear':
        run('cache:clear', $kernel);
        run('config:clear', $kernel);
        run('route:clear', $kernel);
        run('view:clear', $kernel);
        break;

    case 'optimize':
        run('config:cache', $kernel);
        run('route:cache', $kernel);
        run('view:cache', $kernel);
        break;

    case 'storage_link':
        run('storage:link', $kernel);
        break;

    case 'status':
        echo "Pronto para ação. Escolha um comando acima.";
        break;

    default:
        echo "Ação desconhecida.";
        break;
}
