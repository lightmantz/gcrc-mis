<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TherapyRecord extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'child_id', 'therapist_id', 'treatment_plan_id',
        'discipline', 'session_date', 'start_time', 'end_time',
        'duration_minutes', 'session_type', 'location', 'status',
        'record_state', 'finalized_at', 'finalized_by',
        'activities', 'child_response', 'observations', 'recommendations',
        'follow_up_required', 'follow_up_notes',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'finalized_at' => 'timestamp',
            'follow_up_required' => 'boolean',
            'duration_minutes' => 'integer',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function therapist()
    {
        return $this->belongsTo(Staff::class, 'therapist_id');
    }

    public function treatmentPlan()
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function goals()
    {
        return $this->belongsToMany(TreatmentGoal::class, 'therapy_record_goal')
            ->withPivot(['note', 'progress_delta'])
            ->withTimestamps();
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
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

    public function getSessionTypeLabelAttribute(): string
    {
        return match ($this->session_type) {
            'individual'   => 'Individual',
            'group'        => 'Group',
            'consultation' => 'Consultation',
            'home_visit'   => 'Home Visit',
            'telehealth'   => 'Telehealth',
            default        => ucfirst(str_replace('_', ' ', $this->session_type)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'Scheduled',
            'attended'  => 'Attended',
            'missed'    => 'Missed',
            'cancelled' => 'Cancelled',
            'no_show'   => 'No Show',
            default     => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'scheduled' => 'blue',
            'attended'  => 'green',
            'missed'    => 'yellow',
            'cancelled' => 'gray',
            'no_show'   => 'red',
            default     => null,
        };
    }

    public function getRecordStateLabelAttribute(): string
    {
        return match ($this->record_state) {
            'draft'     => 'Draft',
            'finalized' => 'Finalized',
            default     => ucfirst($this->record_state),
        };
    }

    public function getRecordStateToneAttribute(): ?string
    {
        return match ($this->record_state) {
            'draft'     => 'yellow',
            'finalized' => 'green',
            default     => null,
        };
    }

    public function getDurationForHumansAttribute(): string
    {
        $minutes = $this->duration_minutes;

        if ($minutes < 60) {
            return "{$minutes} min";
        }

        $hours = intdiv($minutes, 60);
        $remainder = $minutes % 60;

        return $remainder === 0
            ? "{$hours}h"
            : "{$hours}h {$remainder}m";
    }

    // ─── State helpers ────────────────────────────────

    public function isDraft(): bool
    {
        return $this->record_state === 'draft';
    }

    public function isFinalized(): bool
    {
        return $this->record_state === 'finalized';
    }

    public function wasAttended(): bool
    {
        return $this->status === 'attended';
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeForChild($query, int $childId)
    {
        return $query->where('child_id', $childId);
    }

    public function scopeForTherapist($query, int $staffId)
    {
        return $query->where('therapist_id', $staffId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('session_date', $date);
    }

    public function scopeDraft($query)
    {
        return $query->where('record_state', 'draft');
    }

    public function scopeFinalized($query)
    {
        return $query->where('record_state', 'finalized');
    }

    public function scopeOfDiscipline($query, string $discipline)
    {
        return $query->where('discipline', $discipline);
    }

    public function scopeAttended($query)
    {
        return $query->where('status', 'attended');
    }
}