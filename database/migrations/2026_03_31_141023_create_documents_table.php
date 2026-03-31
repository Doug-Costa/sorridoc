<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->enum('category', ['Contrato', 'Ordem', 'Compliance']);
            $table->enum('sensitivity_level', ['Normal', 'Sigiloso', 'LGPD'])->default('Normal');
            $table->enum('status', ['Pendente', 'Em Aprovação', 'Aprovado', 'Rejeitado'])->default('Pendente');
            $table->string('hash_sha256')->nullable();
            $table->integer('version_number')->default(1);
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
