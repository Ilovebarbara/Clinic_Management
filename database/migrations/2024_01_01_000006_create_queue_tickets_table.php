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
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            
            // Queue Information
            $table->string('queue_number', 20)->unique();
            $table->enum('ticket_type', ['appointment', 'walk_in']);
            $table->string('transaction_type', 50);
            
            // Priority System
            $table->integer('priority_level')->default(4); // 1=Faculty, 2=Personnel, 3=Senior Citizens, 4=Regular
            $table->enum('priority_type', ['faculty', 'personnel', 'senior_citizen', 'regular'])->nullable();
            
            // Queue Management
            $table->integer('window_number')->nullable();
            $table->enum('status', ['waiting', 'serving', 'completed', 'cancelled', 'no_show'])->default('waiting');
            $table->dateTime('called_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            
            // Walk-in patient information (for non-registered patients)
            $table->string('walk_in_name', 100)->nullable();
            $table->enum('walk_in_type', ['student', 'faculty', 'personnel', 'visitor'])->nullable();
            
            // Additional fields
            $table->integer('estimated_wait_time')->nullable(); // in minutes
            $table->text('service_notes')->nullable();
            
            $table->timestamps();

            $table->index(['status', 'priority_level', 'created_at']);
            $table->index(['queue_number']);
            $table->index(['transaction_type', 'status']);
            $table->index(['window_number', 'status']);
            $table->index('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
