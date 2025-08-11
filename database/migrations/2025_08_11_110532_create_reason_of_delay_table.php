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
        Schema::create('reason_of_delay', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->unsignedBigInteger('hcs_id');
            $table->foreign('hcs_id')->references('id')->on('happy_call_status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reason_of_delay');
    }
};
