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

        $table->string('reference_no')->unique();

        $table->foreignId('doctor_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('patient_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->date('appointment_date');

        $table->time('start_time');
        $table->time('end_time');

        $table->enum('status', [
            'booked',
            'cancelled',
            'completed'
        ])->default('booked');

        $table->timestamps();

        $table->unique([
            'doctor_id',
            'appointment_date',
            'start_time'
        ]);
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
