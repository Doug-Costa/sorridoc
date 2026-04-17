<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_name');
            $table->string('fantasy_name')->nullable();
            $table->string('cnpj')->unique();
            $table->string('ie')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_role')->nullable();
            $table->string('registration_token', 64)->unique()->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['Ativo', 'Inativo', 'Pendente'])->default('Pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
