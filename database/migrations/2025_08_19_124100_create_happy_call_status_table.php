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
        Schema::create('happy_call_status', function (Blueprint $table) {
            $table->id();
            $table->date('resolved_date');
            $table->date('happy_call_date');
            $table->string('customer_satisfied');
            $table->text('delay_reason');
            $table->text('voice_of_customer');
            $table->unsignedBigInteger('ici_id');
            $table->timestamps();

            $table->foreign('ici_id')->references('id')->on('initial_customer_information')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('happy_call_status');
    }
};
