<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_goals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('treatment_plan_id')->constrained()->cascadeOnDelete();

            // Ordering within the plan
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Goal identity
            $table->string('title', 200);
            $table->text('description')->nullable();

            // Category — controlled vocabulary
            $table->string('category', 60);

            // Clinical triplet: baseline, target, measure
            $table->text('baseline')->nullable();
            $table->text('target')->nullable();
            $table->text('measure')->nullable();

            // Priority & timeline
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->date('target_date')->nullable();

            // Lifecycle
            $table->enum('status', [
                'not_started', 'in_progress', 'achieved',
                'partially_achieved', 'discontinued',
            ])->default('not_started');

            $table->unsignedTinyInteger('progress_percentage')->default(0);

            // Notes on the goal itself (stable, not timestamped — progress log goes in the other table)
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['treatment_plan_id', 'sort_order']);
            $table->index('category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_goals');
    }
};