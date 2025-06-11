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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('issued_by_id')->constrained('users')->onDelete('cascade');
            
            $table->enum('certificate_type', ['absence', 'employment', 'ojt', 'clearance', 'fitness', 'vaccination', 'laboratory', 'general']);
            $table->string('certificate_number', 50)->unique();
            $table->text('diagnosis')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('purpose')->nullable();
            $table->enum('status', ['draft', 'issued', 'expired', 'revoked'])->default('draft');
            $table->dateTime('issued_at')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();

            $table->index(['patient_id', 'certificate_type']);
            $table->index(['status', 'issued_at']);
            $table->index(['certificate_number']);
            $table->index('doctor_id');
            $table->index('issued_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
