<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapy_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained('staff')->restrictOnDelete();

            // Optional link to the treatment plan this session progressed
            $table->foreignId('treatment_plan_id')->nullable()
                ->constrained('treatment_plans')->nullOnDelete();

            // Discipline — matches the treatment plan vocabulary
            $table->enum('discipline', [
                'physiotherapy',
                'occupational_therapy',
                'speech',
                'psychology',
                'social_work',
                'education',
                'multi_disciplinary',
            ]);

            // Timing
            $table->date('session_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedSmallInteger('duration_minutes');

            // Context
            $table->enum('session_type', [
                'individual', 'group', 'consultation', 'home_visit', 'telehealth',
            ])->default('individual');
            $table->string('location', 120)->nullable();

            // Status — the lifecycle
            $table->enum('status', [
                'scheduled', 'attended', 'missed', 'cancelled', 'no_show',
            ])->default('attended');

            // Finalization state
            $table->enum('record_state', ['draft', 'finalized'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();

            // Content
            $table->text('activities')->nullable();
            $table->text('child_response')->nullable();
            $table->text('observations')->nullable();
            $table->text('recommendations')->nullable();

            // Follow-up
            $table->boolean('follow_up_required')->default(false);
            $table->text('follow_up_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['child_id', 'session_date']);
            $table->index(['therapist_id', 'session_date']);
            $table->index('session_date');
            $table->index('record_state');
            $table->index(['discipline', 'session_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapy_records');
    }
};