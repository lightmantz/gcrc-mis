<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Document extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'documentable_type', 'documentable_id',
        'original_name', 'stored_path', 'mime_type', 'size_bytes',
        'title', 'category', 'description',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'referral_letter'   => 'Referral Letter',
            'medical_report'    => 'Medical Report',
            'assessment_report' => 'Assessment Report',
            'consent_form'      => 'Consent Form',
            'discharge_document'=> 'Discharge Document',
            'identification'    => 'Identification',
            'photo'             => 'Photo',
            'other'             => 'Other',
            default             => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = $this->size_bytes;

        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return round($bytes / (1024 * 1024), 2) . ' MB';
    }
}