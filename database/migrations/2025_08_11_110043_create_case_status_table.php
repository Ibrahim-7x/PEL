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
        Schema::create('case_status', function (Blueprint $table) {
            $table->id();
            $table->string('status'); // e.g., Cancelled, Hold_PNA, etc.
            $table->unsignedBigInteger('ici_id');
            $table->foreign('ici_id')->references('id')->on('initial_customer_information')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_status');
    }
};
