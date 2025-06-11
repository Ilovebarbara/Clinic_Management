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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_number', 20)->unique()->index();
            $table->string('email', 120)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            
            // Personal Information
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->integer('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->date('date_of_birth');
            
            // Contact Information
            $table->string('phone', 20);
            
            // Address
            $table->string('lot_number', 20)->nullable();
            $table->string('barangay_subdivision', 100)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('city_municipality', 100);
            $table->string('province', 100);
            
            // Academic/Work Information
            $table->string('campus', 50);
            $table->string('college_office', 100);
            $table->string('course_designation', 100);
            $table->enum('patient_type', ['student', 'faculty', 'non_academic']);
            
            // Emergency Contact
            $table->string('emergency_contact_name', 100);
            $table->string('emergency_contact_relation', 50);
            $table->string('emergency_contact_number', 20);
            
            // Medical Information
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->text('allergies')->nullable();
            
            // System fields
            $table->string('profile_picture')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();

            $table->index(['patient_type', 'is_active']);
            $table->index(['campus', 'college_office']);
            $table->index('created_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
