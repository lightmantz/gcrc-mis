<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('first_name', 80);
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80);
            $table->string('preferred_name', 80)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('national_id', 50)->nullable()->unique();
            $table->string('photo_path')->nullable();

            // Contact
            $table->string('phone', 40);
            $table->string('alternate_phone', 40)->nullable();
            $table->string('email', 180)->nullable();
            $table->text('address')->nullable();
            $table->string('district', 100)->nullable();
            $table->string('region', 100)->nullable();

            // Background
            $table->string('occupation', 120)->nullable();
            $table->string('employer', 120)->nullable();
            $table->string('education_level', 80)->nullable();

            // Administrative
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('last_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};