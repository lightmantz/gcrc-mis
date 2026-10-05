<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Child extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        // Identity
        'child_number',
        'first_name', 'middle_name', 'last_name', 'preferred_name',
        'date_of_birth', 'gender', 'photo_path',

        // Contact
        'phone', 'email', 'address', 'district', 'region',

        // Medical
        'blood_type', 'allergies', 'chronic_conditions', 'current_medications',
        'disability_summary', 'primary_condition', 'primary_condition_other',

        // Special care
        'special_care_requirements', 'feeding_requirements',
        'mobility_notes', 'communication_notes', 'requires_constant_supervision',

        // Administrative
        'registration_date', 'referred_by', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'registration_date' => 'date',
            'requires_constant_supervision' => 'boolean',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'child_guardian')
            ->withPivot([
                'relationship', 'relationship_other',
                'is_primary', 'is_legal',
                'consent_medical', 'consent_education', 'consent_photography',
                'lives_with_child', 'notes',
            ])
            ->withTimestamps();
    }

    public function primaryGuardian()
    {
        return $this->guardians()->wherePivot('is_primary', true)->first();
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class)->orderBy('priority');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class)->latest('referral_date');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class)->latest('assessment_date');
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class)->latest('diagnosed_at');
    }

    public function activeDiagnoses()
    {
        return $this->diagnoses()->where('status', 'active');
    }

    public function primaryDiagnoses()
    {
        return $this->diagnoses()
            ->where('diagnosis_type', 'primary')
            ->where('status', 'active');
    }

    // ─── Accessors ─────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->preferred_name ?: $this->first_name;
    }

    public function getInitialsAttribute(): string
    {
        $first = mb_substr($this->first_name, 0, 1);
        $last = mb_substr($this->last_name, 0, 1);

        return mb_strtoupper($first . $last);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getAgeDisplayAttribute(): string
    {
        if (! $this->date_of_birth) {
            return '—';
        }

        $age = $this->date_of_birth->age;

        // Under 2 years old, show months — clinically meaningful for therapy
        if ($age < 1) {
            $months = (int) $this->date_of_birth->diffInMonths(now());

            return $months . ' ' . ($months === 1 ? 'month' : 'months');
        }

        return $age . ' ' . ($age === 1 ? 'year' : 'years');
    }

    public function getPrimaryConditionLabelAttribute(): string
    {
        if (! $this->primary_condition) {
            return '—';
        }

        if ($this->primary_condition === 'other' && $this->primary_condition_other) {
            return $this->primary_condition_other;
        }

        return match ($this->primary_condition) {
            'cerebral_palsy'           => 'Cerebral Palsy',
            'down_syndrome'            => 'Down Syndrome',
            'autism_spectrum'          => 'Autism Spectrum Disorder',
            'intellectual_disability'  => 'Intellectual Disability',
            'physical_disability'      => 'Physical Disability',
            'hearing_impairment'       => 'Hearing Impairment',
            'visual_impairment'        => 'Visual Impairment',
            'speech_language_disorder' => 'Speech / Language Disorder',
            'learning_disability'      => 'Learning Disability',
            'multiple_disabilities'    => 'Multiple Disabilities',
            'other'                    => 'Other',
            default                    => ucfirst(str_replace('_', ' ', $this->primary_condition)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'     => 'Active',
            'on_hold'    => 'On Hold',
            'discharged' => 'Discharged',
            'deceased'   => 'Deceased',
            default      => ucfirst($this->status),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'active'     => 'green',
            'on_hold'    => 'yellow',
            'discharged' => 'blue',
            'deceased'   => 'red',
            default      => null,
        };
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInCondition($query, string $condition)
    {
        return $query->where('primary_condition', $condition);
    }

    /**
     * Search by name, child number, or phone.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('middle_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('preferred_name', 'like', "%{$term}%")
              ->orWhere('child_number', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }
}