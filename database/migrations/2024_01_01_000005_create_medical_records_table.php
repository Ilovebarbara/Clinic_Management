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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->foreignId('attending_staff_id')->constrained('users')->onDelete('cascade');
            
            // Basic Information
            $table->string('transaction_type', 50);
            $table->dateTime('date_time');
            $table->text('transaction_details')->nullable();
            
            // Vital Signs
            $table->decimal('height', 5, 2)->nullable(); // in cm
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->integer('hr')->nullable(); // Heart Rate
            $table->integer('rr')->nullable(); // Respiratory Rate
            $table->decimal('temperature', 4, 2)->nullable(); // in Celsius
            $table->integer('bp_systolic')->nullable(); // Blood Pressure Systolic
            $table->integer('bp_diastolic')->nullable(); // Blood Pressure Diastolic
            $table->integer('pain_scale')->nullable(); // 0-10
            $table->text('other_symptoms')->nullable();
            $table->text('assessment')->nullable();
            
            // Diagnosis
            $table->string('initial_diagnosis', 100)->nullable();
            
            $table->timestamps();

            $table->index(['patient_id', 'date_time']);
            $table->index(['doctor_id', 'date_time']);
            $table->index(['transaction_type', 'date_time']);
            $table->index('attending_staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
