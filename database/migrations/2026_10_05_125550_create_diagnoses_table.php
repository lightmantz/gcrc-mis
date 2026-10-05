<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('diagnosed_by')->constrained('staff')->restrictOnDelete();

            // Optional link to the assessment that informed this diagnosis
            $table->foreignId('assessment_id')->nullable()
                ->constrained('assessments')->nullOnDelete();

            // Condition identification
            $table->string('condition_key', 80);        // stable identifier from vocabulary
            $table->string('condition_label', 200);     // human-readable display
            $table->string('condition_other', 200)->nullable(); // free text when key = "other"

            // Clinical classification
            $table->enum('diagnosis_type', ['primary', 'secondary'])->default('primary');
            $table->enum('severity', [
                'mild', 'moderate', 'severe', 'profound', 'unspecified',
            ])->default('unspecified');

            // Lifecycle
            $table->enum('status', [
                'active', 'resolved', 'ruled_out', 'transferred',
            ])->default('active');

            $table->date('diagnosed_at');
            $table->date('ended_at')->nullable();
            $table->text('ended_reason')->nullable();

            // Clinical certainty
            $table->boolean('confirmed')->default(true);

            // Clinical detail
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['child_id', 'status']);
            $table->index(['child_id', 'diagnosis_type']);
            $table->index('condition_key');
            $table->index('diagnosed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};