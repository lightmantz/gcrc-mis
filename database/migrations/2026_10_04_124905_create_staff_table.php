<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('staff_number', 30)->unique();
            $table->string('first_name', 80);
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80);
            $table->string('preferred_name', 80)->nullable();

            // Personal
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('national_id', 50)->nullable()->unique();

            // Contact
            $table->string('phone', 40);
            $table->string('email', 180)->nullable();
            $table->text('address')->nullable();

            // Emergency contact
            $table->string('emergency_contact_name', 120)->nullable();
            $table->string('emergency_contact_phone', 40)->nullable();
            $table->string('emergency_contact_relation', 60)->nullable();

            // Employment
            $table->enum('category', ['management', 'clinical', 'therapy', 'education', 'admin', 'support']);
            $table->string('department', 120)->nullable();
            $table->string('job_title', 120)->nullable();
            $table->text('professional_qualifications')->nullable();
            $table->string('specialization', 120)->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'volunteer', 'intern']);
            $table->date('employment_start_date');
            $table->date('employment_end_date')->nullable();
            $table->enum('status', ['active', 'on_leave', 'suspended', 'terminated'])->default('active');

            // Media & notes
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();

            // Sensitive
            $table->decimal('salary', 12, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('department');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};