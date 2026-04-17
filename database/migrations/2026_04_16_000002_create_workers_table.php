<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('cpf', 14)->unique();
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('role')->nullable();
            $table->string('department')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('gender', ['M', 'F', 'Outro'])->nullable();
            $table->string('access_token', 64)->unique()->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_access_at')->nullable();
            $table->enum('status', ['Ativo', 'Inativo'])->default('Ativo');
            $table->timestamps();

            $table->index(['cpf', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
