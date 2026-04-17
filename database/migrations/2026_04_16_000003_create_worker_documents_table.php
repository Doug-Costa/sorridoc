<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_name');
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('hash_sha256')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['Pendente', 'Aprovado', 'Rejeitado'])->default('Aprovado');
            $table->timestamps();

            $table->index(['worker_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_documents');
    }
};
