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
        Schema::create('ici_logs', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number');
            $table->string('ticket_number');
            $table->enum('action', ['CREATED', 'UPDATED']);
            $table->text('case_status')->nullable();
            $table->string('escalation_level');
            $table->text('voice_of_customers')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ici_logs');
    }
};

