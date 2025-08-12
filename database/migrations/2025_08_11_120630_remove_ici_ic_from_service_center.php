<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_center', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['ici_id']);

            // Then drop the column
            $table->dropColumn('ici_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_center', function (Blueprint $table) {
            $table->unsignedBigInteger('ici_id')->nullable();

            // Add the foreign key back
            $table->foreign('ici_id')->references('id')->on('ici')->onDelete('cascade');
        });
    }
};