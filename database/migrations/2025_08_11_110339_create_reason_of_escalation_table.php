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
        Schema::create('reason_of_escalation', function (Blueprint $table) {
             $table->id();
            $table->string('reason');
            $table->unsignedBigInteger('ici_id');
            $table->foreign('ici_id')->references('id')->on('initial_customer_information')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reason_of_escalation');
    }
};
