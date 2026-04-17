<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SorriMedSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'corporate_name' => 'Empresa Demo LTDA',
            'fantasy_name' => 'Demo',
            'cnpj' => '12.345.678/0001-90',
            'ie' => '123456789',
            'email' => 'contato@demo.com.br',
            'phone' => '(44) 99999-9999',
            'address' => 'Rua Demo, 100 - Centro',
            'city' => 'Maringá',
            'state' => 'PR',
            'zip_code' => '87000-000',
            'responsible_name' => 'João Gestor',
            'responsible_role' => 'Gerente de RH',
            'status' => 'Ativo',
        ]);

        $plainToken = $company->generateRegistrationToken(30);
        $this->command->info("Token de registro da empresa: {$plainToken}");
        $this->command->info('URL de acesso: '.url('/rh/'.$plainToken));

        $user = User::create([
            'name' => 'João Gestor (RH)',
            'email' => 'rh@demo.com.br',
            'password' => Hash::make('password'),
            'role' => 'Gestor RH',
            'company_id' => $company->id,
        ]);

        $this->command->info('Usuário Gestor RH criado:');
        $this->command->info('Email: rh@demo.com.br');
        $this->command->info('Senha: password');

        $workers = [
            [
                'name' => 'Maria da Silva',
                'cpf' => '123.456.789-00',
                'role' => 'Auxiliar Administrativo',
                'department' => 'Administrativo',
                'email' => 'maria@demo.com.br',
            ],
            [
                'name' => 'José Santos',
                'cpf' => '234.567.890-11',
                'role' => 'Técnico em Enfermagem',
                'department' => 'Enfermagem',
                'email' => 'jose@demo.com.br',
            ],
            [
                'name' => 'Ana Oliveira',
                'cpf' => '345.678.901-22',
                'role' => 'Dentista',
                'department' => 'Odontologia',
                'email' => 'ana@demo.com.br',
            ],
        ];

        foreach ($workers as $workerData) {
            $worker = Worker::create([
                'company_id' => $company->id,
                'name' => $workerData['name'],
                'cpf' => $workerData['cpf'],
                'role' => $workerData['role'],
                'department' => $workerData['department'],
                'email' => $workerData['email'],
                'status' => 'Ativo',
            ]);

            $plainWorkerToken = $worker->generateAccessToken(365);
            $this->command->info("Token do trabalhador {$worker->name}: {$plainWorkerToken}");
        }

        $this->command->info('');
        $this->command->info('=== Dados para Teste ===');
        $this->command->info('Portal RH: '.url('/rh/'.$plainToken));
        $this->command->info('Portal Trabalhador: '.url('/worker'));
    }
}
