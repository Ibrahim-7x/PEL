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
            $table->timestamp('complaint_escalation_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('initial_customer_information', function (Blueprint $table) {
            $table->date('complaint_escalation_date')->change();
        });
    }
};
