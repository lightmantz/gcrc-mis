<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Referral extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'child_id',
        'source_type', 'source_name', 'source_contact',
        'referral_date', 'reason', 'status', 'notes',
        'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'referral_date' => 'date',
        ];
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getSourceTypeLabelAttribute(): string
    {
        return match ($this->source_type) {
            'walk_in'   => 'Walk-in',
            'hospital'  => 'Hospital',
            'clinic'    => 'Clinic',
            'school'    => 'School',
            'community' => 'Community',
            'self'      => 'Self',
            'other'     => 'Other',
            default     => ucfirst($this->source_type),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'received'    => 'Received',
            'accepted'    => 'Accepted',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'declined'    => 'Declined',
            default       => ucfirst($this->status),
        };
    }

    public function getStatusToneAttribute(): ?string
    {
        return match ($this->status) {
            'received'    => 'blue',
            'accepted'    => 'green',
            'in_progress' => 'yellow',
            'completed'   => 'green',
            'declined'    => 'red',
            default       => null,
        };
    }
}