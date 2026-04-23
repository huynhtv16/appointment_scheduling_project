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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_patient');
            $table->dateTime('date');
            $table->string('symptom');
            $table->unsignedBigInteger('id_doctor');
            $table->integer('id_service');
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('id_patient')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('id_doctor')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
