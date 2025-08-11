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
        Schema::create('initial_customer_information', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->date('complaint_escalation_date');
            $table->string('agent_name');
            $table->text('voice_of_customer');
            $table->integer('aging');
            $table->unsignedBigInteger('u_id');
            $table->foreign('u_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initial_customer_information');
    }
};
