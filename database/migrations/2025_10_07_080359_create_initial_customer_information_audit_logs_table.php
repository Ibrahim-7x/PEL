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
        Schema::create('initial_customer_information_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number');
            $table->string('ticket_no');
            $table->enum('action', ['CREATED', 'UPDATED']);
            $table->string('escalation_level');
            $table->json('old_values')->nullable(); // For updates - store previous values
            $table->json('new_values')->nullable(); // For updates - store new values
            $table->json('changed_fields')->nullable(); // List of fields that were changed
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable(); // Additional context
            $table->timestamp('action_timestamp');
            $table->timestamps();

            // Index for faster queries (shortened names for MySQL compatibility)
            $table->index(['complaint_number', 'action_timestamp'], 'ici_audit_complaint_idx');
            $table->index(['ticket_no', 'action_timestamp'], 'ici_audit_ticket_idx');
            $table->index('action_timestamp', 'ici_audit_timestamp_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initial_customer_information_audit_logs');
    }
};
