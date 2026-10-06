<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TreatmentGoalProgress extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'treatment_goal_progress';

    protected $fillable = [
        'treatment_goal_id', 'recorded_by',
        'recorded_on', 'note',
        'progress_percentage', 'status_at_record',
    ];

    protected function casts(): array
    {
        return [
            'recorded_on' => 'date',
            'progress_percentage' => 'integer',
        ];
    }

    public function goal()
    {
        return $this->belongsTo(TreatmentGoal::class, 'treatment_goal_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_at_record) {
            'not_started'        => 'Not Started',
            'in_progress'        => 'In Progress',
            'achieved'           => 'Achieved',
            'partially_achieved' => 'Partially Achieved',
            'discontinued'       => 'Discontinued',
            default              => '—',
        };
    }
}