<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Advogado', 'Diretor', 'Operacional', 'Gestor RH', 'Empresa', 'Funcionario') DEFAULT 'Operacional'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Advogado', 'Diretor', 'Operacional') DEFAULT 'Operacional'");
        }
    }


};
