<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TreatmentPlan extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'child_id', 'lead_staff_id', 'discipline', 'status',
        'start_date', 'target_review_date', 'end_date',
        'review_cycle', 'last_reviewed_at', 'next_review_date',
        'overall_objectives', 'notes',
        'activated_at', 'activated_by', 'closed_at', 'closed_by', 'closure_reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date'         => 'date',
            'target_review_date' => 'date',
            'end_date'           => 'date',
            'last_reviewed_at'   => 'date',
            'next_review_date'   => 'date',
            'activated_at'       => 'timestamp',
            'closed_at'          => 'timestamp',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function leadStaff()
    {
        return $this->belongsTo(Staff::class, 'lead_staff_id');
    }

    public function teamMembers()
    {
        return $this->belongsToMany(Staff::class, 'treatment_plan_staff')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function diagnoses()
    {
        return $this->belongsToMany(Diagnosis::class, 'treatment_plan_diagnosis')
            ->withTimestamps();
    }

    public function goals()
    {
        return $this->hasMany(TreatmentGoal::class)->orderBy('sort_order');
    }

    public function activatedBy()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // ─── Accessors ─────────────────────────────────────

    public function getDisciplineLabelAttribute(): string
    {
        return match ($this->discipline) {
            'physiotherapy'        => 'Physiotherapy',
            'occupational_therapy' => 'Occupational Therapy',
            'speech'               => 'Speech & Language',
            'psychology'           => 'Psychology',
            'social_work'          => 'Social Work',
            'education'            => 'Education',
            'multi_disciplinary'   => 'Multi-Disciplinary',
            default                => ucfirst(str_replace('_', ' ', $this->discipline)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'        => 'Draft',
            'active'       => 'Active',
            'under_review' => 'Under Review',
            'completed'    => 'Completed',
            'cancelled'    => 'Cancelled',
            default        => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'draft'        => 'yellow',
            'active'       => 'green',
            'under_review' => 'blue',
            'completed'    => 'teal',
            'cancelled'    => 'red',
            default        => null,
        };
    }

    public function getReviewCycleLabelAttribute(): string
    {
        return match ($this->review_cycle) {
            'weekly'    => 'Weekly',
            'biweekly'  => 'Every 2 weeks',
            'monthly'   => 'Monthly',
            'quarterly' => 'Quarterly',
            'ad_hoc'    => 'As needed',
            default     => ucfirst($this->review_cycle),
        };
    }

    // ─── Aggregates ────────────────────────────────────

    public function getTotalGoalsAttribute(): int
    {
        return $this->goals->count();
    }

    public function getAchievedGoalsAttribute(): int
    {
        return $this->goals->where('status', 'achieved')->count();
    }

    public function getOverallProgressAttribute(): ?int
    {
        if ($this->goals->isEmpty()) {
            return null;
        }

        return (int) round($this->goals->avg('progress_percentage'));
    }

    // ─── State helpers ────────────────────────────────

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'under_review'], true);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['completed', 'cancelled'], true);
    }

    public function isOverdueForReview(): bool
    {
        return $this->next_review_date
            && $this->next_review_date->isPast()
            && $this->isActive();
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeForChild($query, int $childId)
    {
        return $query->where('child_id', $childId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'under_review']);
    }

    public function scopeOfDiscipline($query, string $discipline)
    {
        return $query->where('discipline', $discipline);
    }

    public function scopeOverdueReview($query)
    {
        return $query->whereNotNull('next_review_date')
            ->whereDate('next_review_date', '<', now())
            ->active();
    }
}