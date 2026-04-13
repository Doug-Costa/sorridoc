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
        Schema::table('approvals', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->after('owner_id');
            $table->foreign('assigned_to')->references('id')->on('users');
        });

        // Populate existing records
        DB::table('approvals')->update(['assigned_to' => DB::raw('owner_id')]);

        // Make it NOT NULL
        Schema::table('approvals', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }
};
