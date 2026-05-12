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
        Schema::connection('ai_pgsql')->table('document_chunks', function (Blueprint $table) {
            $table->vector('embedding', 384)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('ai_pgsql')->table('document_chunks', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
