<?php

namespace App\Models;

use App\Support\GoalCategories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TreatmentGoal extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'treatment_plan_id', 'sort_order',
        'title', 'description', 'category',
        'baseline', 'target', 'measure',
        'priority', 'target_date',
        'status', 'progress_percentage', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'progress_percentage' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function plan()
    {
        return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }

    public function progressNotes()
    {
        return $this->hasMany(TreatmentGoalProgress::class)->latest('recorded_on');
    }

    // ─── Accessors ─────────────────────────────────────

    public function getCategoryLabelAttribute(): string
    {
        return GoalCategories::label($this->category);
    }

    public function getCategoryToneAttribute(): ?string
    {
        return GoalCategories::tone($this->category);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'high'   => 'High',
            'medium' => 'Medium',
            'low'    => 'Low',
            default  => ucfirst($this->priority),
        };
    }

    public function getPriorityToneAttribute(): ?string
    {
        return match ($this->priority) {
            'high'   => 'red',
            'medium' => 'yellow',
            'low'    => 'blue',
            default  => null,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_started'         => 'Not Started',
            'in_progress'         => 'In Progress',
            'achieved'            => 'Achieved',
            'partially_achieved'  => 'Partially Achieved',
            'discontinued'        => 'Discontinued',
            default               => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'not_started'        => 'gray',
            'in_progress'        => 'blue',
            'achieved'           => 'green',
            'partially_achieved' => 'yellow',
            'discontinued'       => 'red',
            default              => null,
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->target_date
            && $this->target_date->isPast()
            && ! in_array($this->status, ['achieved', 'discontinued'], true);
    }

    // ─── Helpers ──────────────────────────────────────

    public function isAchieved(): bool
    {
        return $this->status === 'achieved';
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['not_started', 'in_progress'], true);
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
public function therapyRecords()
{
    return $this->belongsToMany(TherapyRecord::class, 'therapy_record_goal')
        ->withPivot(['note', 'progress_delta'])
        ->withTimestamps();
}
    public function scopeAchieved($query)
    {
        return $query->where('status', 'achieved');
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('target_date')
            ->whereDate('target_date', '<', now())
            ->whereNotIn('status', ['achieved', 'discontinued']);
    }
}