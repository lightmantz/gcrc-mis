<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_plan_diagnosis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('treatment_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnosis_id')->constrained()->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['treatment_plan_id', 'diagnosis_id'], 'tpd_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_plan_diagnosis');
    }
};