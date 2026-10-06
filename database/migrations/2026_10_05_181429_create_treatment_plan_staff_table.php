<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_plan_staff', function (Blueprint $table) {
            $table->id();

            $table->foreignId('treatment_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();

            // Role in this plan: physiotherapist, OT, nurse, teacher, social worker, etc.
            $table->string('role', 80)->nullable();

            $table->timestamps();

            $table->unique(['treatment_plan_id', 'staff_id'], 'tps_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_plan_staff');
    }
};