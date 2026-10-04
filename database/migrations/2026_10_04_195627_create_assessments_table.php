<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessor_id')->constrained('staff')->restrictOnDelete();

            // Which assessment type this is
            $table->string('type', 60);

            // When the assessment was actually performed (may differ from created_at)
            $table->date('assessment_date');

            // Lifecycle
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();

            // Narrative summary by the assessor
            $table->text('summary')->nullable();

            // Clinical recommendations arising from this assessment
            $table->text('recommendations')->nullable();

            // Structured, type-specific data as JSON
            $table->json('findings')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['child_id', 'assessment_date']);
            $table->index(['type', 'status']);
            $table->index('assessment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};