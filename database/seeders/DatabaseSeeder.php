<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SorriMedSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Dra. Regina Alves (Super Admin)',
            'email' => 'admin@sorridoc.com.br',
            'role' => 'Super Admin',
            'unit' => 'Maringá',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'João Diretor',
            'email' => 'diretor@sorridoc.com.br',
            'role' => 'Diretor',
            'unit' => 'Sorriso',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Maria (Operacional)',
            'email' => 'operacional@sorridoc.com.br',
            'role' => 'Operacional',
            'unit' => 'Sorriso',
            'password' => Hash::make('password'),
        ]);
    }
}
