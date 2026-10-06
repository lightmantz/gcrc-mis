<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class Staff extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'staff';

    protected $fillable = [
        'staff_number',
        'first_name', 'middle_name', 'last_name', 'preferred_name',
        'date_of_birth', 'gender', 'national_id',
        'phone', 'email', 'address',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        'category', 'department', 'job_title', 'professional_qualifications',
        'specialization', 'employment_type', 'employment_start_date',
        'employment_end_date', 'status',
        'photo_path', 'notes', 'salary',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'employment_start_date' => 'date',
            'employment_end_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function users()
    {
        return $this->belongsToMany(User::class, 'staff_user')
            ->withTimestamps();
    }

    public function treatmentPlansLed()
    {
        return $this->hasMany(TreatmentPlan::class, 'lead_staff_id');
    }

    public function treatmentPlanTeams()
    {
        return $this->belongsToMany(TreatmentPlan::class, 'treatment_plan_staff')
            ->withPivot('role')
            ->withTimestamps();
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

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'management' => 'Management',
            'clinical'   => 'Clinical',
            'therapy'    => 'Therapy',
            'education'  => 'Education',
            'admin'      => 'Administration',
            'support'    => 'Support',
            default      => ucfirst($this->category),
        };
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return match ($this->employment_type) {
            'full_time' => 'Full-time',
            'part_time' => 'Part-time',
            'contract'  => 'Contract',
            'volunteer' => 'Volunteer',
            'intern'    => 'Intern',
            default     => ucfirst($this->employment_type),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'     => 'Active',
            'on_leave'   => 'On Leave',
            'suspended'  => 'Suspended',
            'terminated' => 'Terminated',
            default      => ucfirst($this->status),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'active'     => 'green',
            'on_leave'   => 'yellow',
            'suspended'  => 'red',
            'terminated' => 'red',
            default      => null,
        };
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
public function therapyRecords()
{
    return $this->hasMany(TherapyRecord::class, 'therapist_id')->latest('session_date');
}
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}