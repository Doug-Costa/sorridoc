<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('documents', 'approvals');

        Schema::table('approvals', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('file_path')->nullable()->change();
        });

        Schema::table('approval_flows', function (Blueprint $table) {
            $table->renameColumn('document_id', 'approval_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_flows', function (Blueprint $table) {
            $table->renameColumn('approval_id', 'document_id');
        });

        Schema::table('approvals', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->dropColumn('description');
        });

        Schema::rename('approvals', 'documents');
    }
};
