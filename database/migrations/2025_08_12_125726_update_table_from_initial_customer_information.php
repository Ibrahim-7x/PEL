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
        Schema::table('initial_customer_information', function (Blueprint $table) {
            $table->string('service_center');
            $table->string('case_status');
            $table->string('complaint_category');
            $table->string('reason_of_escalation');
            $table->string('escalation_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('initial_customer_information', function (Blueprint $table) {
            //
        });
    }
};
