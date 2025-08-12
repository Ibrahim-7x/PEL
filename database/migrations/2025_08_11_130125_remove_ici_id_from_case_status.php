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
        Schema::table('case_status', function (Blueprint $table) {
            $table->dropForeign(['ici_id']);
            $table->dropColumn('ici_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_status', function (Blueprint $table) {
            $table->unsignedBigInteger('ici_id')->nullable();
            $table->foreign('ici_id')->references('id')->on('ici')->onDelete('cascade');
        });
    }
};
