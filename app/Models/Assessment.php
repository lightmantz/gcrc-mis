<?php

namespace App\Models;

use App\Assessments\AssessmentType;
use App\Assessments\AssessmentTypeRegistry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Assessment extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'child_id', 'assessor_id', 'type',
        'assessment_date', 'status',
        'finalized_at', 'finalized_by',
        'summary', 'recommendations', 'findings',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'finalized_at' => 'timestamp',
            'findings' => 'array',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function assessor()
    {
        return $this->belongsTo(Staff::class, 'assessor_id');
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }

    // ─── Type delegation ───────────────────────────────

    /**
     * Returns the AssessmentType instance for this row,
     * or null if the type key is not registered.
     */
    public function typeInstance(): ?AssessmentType
    {
        return AssessmentTypeRegistry::get($this->type);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->typeInstance()?->label()
            ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getAssessorCategoryAttribute(): ?string
    {
        return $this->typeInstance()?->assessorCategory();
    }

    // ─── State checks ──────────────────────────────────

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'finalized' => 'Finalized',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'draft'     => 'yellow',
            'finalized' => 'green',
            default     => null,
        };
    }

    // ─── Helpers ───────────────────────────────────────

    /**
     * Get a single finding value, with optional default.
     */
    public function finding(string $key, mixed $default = null): mixed
    {
        return $this->findings[$key] ?? $default;
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }
}