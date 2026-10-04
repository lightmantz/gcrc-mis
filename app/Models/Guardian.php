<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Guardian extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'preferred_name',
        'date_of_birth', 'gender', 'national_id', 'photo_path',
        'phone', 'alternate_phone', 'email',
        'address', 'district', 'region',
        'occupation', 'employer', 'education_level',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    public function children()
    {
        return $this->belongsToMany(Child::class, 'child_guardian')
            ->withPivot([
                'relationship', 'relationship_other',
                'is_primary', 'is_legal',
                'consent_medical', 'consent_education', 'consent_photography',
                'lives_with_child', 'notes',
            ])
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

    // ─── Scopes ────────────────────────────────────────

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('middle_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}