<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_staff_id')->constrained('staff')->restrictOnDelete();

            // Discipline — one plan per discipline per child
            $table->enum('discipline', [
                'physiotherapy',
                'occupational_therapy',
                'speech',
                'psychology',
                'social_work',
                'education',
                'multi_disciplinary',
            ]);

            // Lifecycle
            $table->enum('status', [
                'draft', 'active', 'under_review', 'completed', 'cancelled',
            ])->default('draft');

            // Dates
            $table->date('start_date');
            $table->date('target_review_date')->nullable();
            $table->date('end_date')->nullable();

            // Review cadence (we'll build the reviews table in 10b)
            $table->enum('review_cycle', [
                'weekly', 'biweekly', 'monthly', 'quarterly', 'ad_hoc',
            ])->default('monthly');
            $table->date('last_reviewed_at')->nullable();
            $table->date('next_review_date')->nullable();

            // Content
            $table->text('overall_objectives')->nullable();
            $table->text('notes')->nullable();

            // Activation / closure audit
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('closure_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['child_id', 'status']);
            $table->index(['child_id', 'discipline']);
            $table->index('status');
            $table->index('next_review_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_plans');
    }
};