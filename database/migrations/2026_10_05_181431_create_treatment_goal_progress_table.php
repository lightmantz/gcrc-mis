<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_goal_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('treatment_goal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('staff')->restrictOnDelete();

            $table->date('recorded_on');
            $table->text('note');

            // Snapshot of status/progress at the time of this note
            $table->unsignedTinyInteger('progress_percentage')->nullable();
            $table->enum('status_at_record', [
                'not_started', 'in_progress', 'achieved',
                'partially_achieved', 'discontinued',
            ])->nullable();

            $table->timestamps();

            $table->index(['treatment_goal_id', 'recorded_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_goal_progress');
    }
};