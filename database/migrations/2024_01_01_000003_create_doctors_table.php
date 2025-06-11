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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('specialization', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20);
            $table->string('license_number', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['specialization', 'is_active']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
