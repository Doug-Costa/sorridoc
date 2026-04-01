<?php

/**
 * SorriDoc - Auxiliar de Deploy (Hostinger Shared)
 * 
 * Acesse este arquivo via navegador: seu-site.com.br/deploy_helper.php
 * RECOMENDADO: Apague este arquivo após o uso por segurança!
 */

// Autoload do Laravel (Ajustado para pasta public)
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h2>SorriDoc Deployment Helper</h2>";

$command = $_GET['cmd'] ?? null;

if (!$command) {
    echo "<ul>";
    echo "<li><a href='?cmd=migrate'>Rodar Migrações (Migrate)</a></li>";
    echo "<li><a href='?cmd=storage_link'>Criar Link Simbólico (Storage:link)</a></li>";
    echo "<li><a href='?cmd=optimize'>Otimizar Tudo (Cache, View, Route)</a></li>";
    echo "<li><a href='?cmd=clear'>Limpar Todos os Caches</a></li>";
    echo "</ul>";
}

try {
    if ($command === 'migrate') {
        Artisan::call('migrate', ['--force' => true]);
        echo "<pre>" . Artisan::output() . "</pre>";
        echo "<p style='color:green'>Migrações executadas!</p>";
    }

    if ($command === 'storage_link') {
        Artisan::call('storage:link');
        echo "<pre>" . Artisan::output() . "</pre>";
        echo "<p style='color:green'>Storage Link criado!</p>";
    }

    if ($command === 'optimize') {
        Artisan::call('optimize');
        echo "<pre>" . Artisan::output() . "</pre>";
        echo "<p style='color:green'>Sistema otimizado!</p>";
    }

    if ($command === 'clear') {
        Artisan::call('optimize:clear');
        echo "<pre>" . Artisan::output() . "</pre>";
        echo "<p style='color:green'>Caches limpos!</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>Erro (Verifique se o banco está configurado no .env): " . $e->getMessage() . "</p>";
}

echo "<br><hr><a href='deploy_helper.php'>Voltar</a>";
