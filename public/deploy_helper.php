<?php

/**
 * SorriDoc - Auxiliar de Deploy (Hostinger Shared)
 */

$rootPath = __DIR__ . '/..';
$envPath = $rootPath . '/.env';
$examplePath = $rootPath . '/.env.example';

// Ação manual: Criar .env se não existir
if (isset($_GET['cmd']) && $_GET['cmd'] === 'create_env') {
    if (file_exists($examplePath)) {
        copy($examplePath, $envPath);
        echo "<p style='color:green'>Arquivo .env criado com sucesso a partir do .env.example!</p>";
        echo "<a href='deploy_helper.php'>Voltar e Continuar</a>";
        exit;
    } else {
        die("Erro fatal: .env.example não foi encontrado no root.");
    }
}

// Verifica se o .env existe antes de carregar o Laravel
if (!file_exists($envPath)) {
    echo "<h2>O arquivo .env não foi encontrado na Hostinger!</h2>";
    echo "<p>Isso acontece porque o Git normalmente ignora esse arquivo por segurança.</p>";
    echo "<a href='?cmd=create_env' style='padding: 10px 20px; background: #4f46e5; color: white; text-decoration: none; border-radius: 5px;'>Criar .env agora</a>";
    echo "<br><br><p>Após criar, você precisará editar os dados do banco no Gerenciador de Arquivos da Hostinger.</p>";
    exit;
}

// Autoload do Laravel
require $rootPath . '/vendor/autoload.php';
$app = require_once $rootPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h2>SorriDoc Deployment Helper</h2>";

$command = $_GET['cmd'] ?? null;

if (!$command) {
    echo "<ul>";
    echo "<li><a href='?cmd=key'>Gerar APP_KEY (Obrigatório se der erro 500)</a></li>";
    echo "<li><a href='?cmd=migrate'>Rodar Migrações (Migrate)</a></li>";
    echo "<li><a href='?cmd=create_admin'>Criar Novo Usuário Admin</a></li>";
    echo "<li><a href='?cmd=storage_link'>Criar Link Simbólico (Storage:link)</a></li>";
    echo "<li><a href='?cmd=optimize'>Otimizar Tudo (Cache, View, Route)</a></li>";
    echo "<li><a href='?cmd=clear'>Limpar Todos os Caches</a></li>";
    echo "</ul>";
}

try {
    if ($command === 'create_admin') {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@sorridoc.com.br'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'pin_code' => '1234', // Definindo um PIN padrão inicial
                'is_super_admin' => true, // Se o seu sistema usa essa flag
            ]
        );
        echo "<pre>Usuário Criado!</pre>";
        echo "<p style='color:green'>Login: <b>admin@sorridoc.com.br</b><br>Senha: <b>admin123</b><br>PIN: <b>1234</b></p>";
    }

    if ($command === 'key') {
        \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
        echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
        echo "<p style='color:green'>Chave de segurança gerada!</p>";
    }

    if ($command === 'migrate') {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
        echo "<p style='color:green'>Migrações executadas!</p>";
    }

    if ($command === 'storage_link') {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
        echo "<p style='color:green'>Storage Link criado!</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>Erro operacional: " . $e->getMessage() . "</p>";
}

echo "<br><hr><a href='deploy_helper.php'>Voltar</a>";
