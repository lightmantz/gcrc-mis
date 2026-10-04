<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('child_number', 30)->unique();
            $table->string('first_name', 80);
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80);
            $table->string('preferred_name', 80)->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('photo_path')->nullable();

            // Contact
            $table->string('phone', 40)->nullable();
            $table->string('email', 180)->nullable();
            $table->text('address')->nullable();
            $table->string('district', 100)->nullable();
            $table->string('region', 100)->nullable();

            // Medical
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'unknown'])
                  ->default('unknown');
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('disability_summary')->nullable();
            $table->enum('primary_condition', [
                'cerebral_palsy',
                'down_syndrome',
                'autism_spectrum',
                'intellectual_disability',
                'physical_disability',
                'hearing_impairment',
                'visual_impairment',
                'speech_language_disorder',
                'learning_disability',
                'multiple_disabilities',
                'other',
            ])->nullable();
            $table->string('primary_condition_other', 120)->nullable();

            // Special Care
            $table->text('special_care_requirements')->nullable();
            $table->text('feeding_requirements')->nullable();
            $table->text('mobility_notes')->nullable();
            $table->text('communication_notes')->nullable();
            $table->boolean('requires_constant_supervision')->default(false);

            // Administrative
            $table->date('registration_date');
            $table->string('referred_by', 120)->nullable();
            $table->enum('status', ['active', 'on_hold', 'discharged', 'deceased'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('primary_condition');
            $table->index('registration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};