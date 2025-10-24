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
        Schema::create('coms', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number', 20)->unique();
            $table->string('job', 255)->nullable();
            $table->timestamp('coms_complaint_date')->nullable();
            $table->string('job_type')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('contact_number', 255)->nullable();
            $table->string('technician_name')->nullable();
            $table->timestamp('date_of_purchase')->nullable();
            $table->string('product')->nullable();
            $table->string('job_status')->nullable();
            $table->string('problem')->nullable();
            $table->text('work_done')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coms');
    }
};
