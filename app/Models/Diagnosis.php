<?php

namespace App\Models;

use App\Diagnoses\DiagnosisVocabulary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Diagnosis extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'child_id', 'diagnosed_by', 'assessment_id',
        'condition_key', 'condition_label', 'condition_other',
        'diagnosis_type', 'severity', 'status',
        'diagnosed_at', 'ended_at', 'ended_reason',
        'confirmed', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'diagnosed_at' => 'date',
            'ended_at'     => 'date',
            'confirmed'    => 'boolean',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function diagnosedBy()
    {
        return $this->belongsTo(Staff::class, 'diagnosed_by');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function treatmentPlans()
    {
        return $this->belongsToMany(TreatmentPlan::class, 'treatment_plan_diagnosis')
            ->withTimestamps();
    }

    // ─── Accessors ─────────────────────────────────────

    /**
     * The display label — uses `condition_other` when the key is "other".
     */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->condition_key === 'other' && $this->condition_other) {
            return $this->condition_other;
        }

        return $this->condition_label ?: DiagnosisVocabulary::label($this->condition_key);
    }

    public function getCategoryAttribute(): ?string
    {
        return DiagnosisVocabulary::category($this->condition_key);
    }

    public function getCategoryLabelAttribute(): ?string
    {
        $key = $this->category;

        return $key ? (DiagnosisVocabulary::CATEGORIES[$key] ?? ucfirst($key)) : null;
    }

    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'mild'        => 'Mild',
            'moderate'    => 'Moderate',
            'severe'      => 'Severe',
            'profound'    => 'Profound',
            'unspecified' => 'Unspecified',
            default       => ucfirst($this->severity),
        };
    }

    public function getSeverityToneAttribute(): ?string
    {
        return match ($this->severity) {
            'mild'        => 'green',
            'moderate'    => 'yellow',
            'severe'      => 'red',
            'profound'    => 'red',
            default       => null,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'Active',
            'resolved'    => 'Resolved',
            'ruled_out'   => 'Ruled Out',
            'transferred' => 'Transferred',
            default       => ucfirst($this->status),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'active'      => 'blue',
            'resolved'    => 'green',
            'ruled_out'   => 'yellow',
            'transferred' => 'cyan',
            default       => null,
        };
    }

    public function getDiagnosisTypeLabelAttribute(): string
    {
        return $this->diagnosis_type === 'primary' ? 'Primary' : 'Secondary';
    }

    public function getDiagnosisTypeToneAttribute(): ?string
    {
        return $this->diagnosis_type === 'primary' ? 'teal' : null;
    }

    /**
     * Number of days this diagnosis has been active (or was active).
     */
    public function getDurationInDaysAttribute(): ?int
    {
        if (! $this->diagnosed_at) {
            return null;
        }

        $end = $this->ended_at ?? now();

        return $this->diagnosed_at->diffInDays($end);
    }

    // ─── State helpers ────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPrimary(): bool
    {
        return $this->diagnosis_type === 'primary';
    }

    public function isProvisional(): bool
    {
        return ! $this->confirmed;
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrimary($query)
    {
        return $query->where('diagnosis_type', 'primary');
    }

    public function scopeSecondary($query)
    {
        return $query->where('diagnosis_type', 'secondary');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('confirmed', true);
    }

    public function scopeProvisional($query)
    {
        return $query->where('confirmed', false);
    }

    public function scopeOfCondition($query, string $key)
    {
        return $query->where('condition_key', $key);
    }
}