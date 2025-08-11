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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('agent')->nullable();
            $table->string('ml1')->nullable();
            $table->string('ml2')->nullable();
            $table->string('ml3')->nullable();
            $table->string('ml4')->nullable();
            $table->unsignedBigInteger('ici_id');
            $table->foreign('ici_id')->references('id')->on('initial_customer_information')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
