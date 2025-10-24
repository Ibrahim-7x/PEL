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
            $table->string('ticket_number')->unique();
            $table->unsignedBigInteger('complaint_id');
            $table->string('service_center');
            $table->timestamp('complaint_escalation_date');
            $table->bigInteger('aging')->nullable();
            $table->string('case_status');
            $table->string('complaint_category');
            $table->string('agent_name');
            $table->string('reason_of_escalation');
            $table->string('escalation_level');
            $table->string('voice_of_customer');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('complaint_id')->references('id')->on('coms')->onDelete('cascade');

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