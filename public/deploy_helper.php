<?php

/**
 * SorriDoc Deploy Helper v2 - Portal SorriMed Update
 * Este script auxilia na sincronização do ambiente em hospedagens compartilhadas.
 */

use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$appPath = __DIR__ . '/../bootstrap/app.php';

if (!file_exists($autoloadPath) || !file_exists($appPath)) {
    die("Erro: Não foi possível localizar o diretório 'vendor' ou 'bootstrap'.");
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
echo "<h1>🚀 SorriDoc Deploy Helper (Portal Update)</h1>";

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

// 1. Migrações (Incluindo a nova worker_id)
runCommand('migrate --force', 'Executando migrações do banco de dados (Novas tabelas e campos do Portal)');

// 2. Limpeza de Caches
runCommand('optimize:clear', 'Limpando e otimizando caches');

// 3. Link simbólico de storage (essencial para download de documentos)
runCommand('storage:link', 'Criando link simbólico para documentos (storage:link)');

echo "<p class='success'>✓ Tudo pronto! O Portal agora está acessível em seu-dominio.com.br/portal</p>";
echo "<hr><p style='color: #ef4444; font-weight: bold; text-align: center;'>⚠️ IMPORTANTE: EXCLUA ESTE ARQUIVO DO SERVIDOR AGORA! ⚠️</p>";
echo "</div></body></html>";

$kernel->terminate($request, $response);
