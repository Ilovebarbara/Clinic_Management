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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->unique();
            $table->string('email', 120)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->enum('role', ['super_admin', 'staff', 'nurse', 'dentist', 'physician'])->default('staff');
            $table->string('phone', 20)->nullable();
            $table->string('employee_id', 20)->unique()->nullable();
            $table->string('profile_picture')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();

            $table->index(['role', 'is_active']);
            $table->index('created_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
