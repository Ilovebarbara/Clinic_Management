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
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('attending_staff_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->dateTime('appointment_date');
            $table->string('appointment_type', 50);
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show', 'rescheduled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->text('reason')->nullable();
            $table->text('symptoms')->nullable();
            $table->string('preferred_time', 50)->nullable();
            
            // Queue management
            $table->string('queue_number', 20)->nullable();
            $table->integer('window_number')->nullable();
            
            $table->timestamps();

            $table->index(['appointment_date', 'status']);
            $table->index(['patient_id', 'status']);
            $table->index(['doctor_id', 'appointment_date']);
            $table->index('attending_staff_id');
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
