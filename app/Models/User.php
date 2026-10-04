<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name', 'email', 'password', 'is_active',
        'deactivated_at', 'deactivated_by', 'deactivation_reason',
        'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    // ─── Relationships ─────────────────────────────────

    /**
     * The staff record(s) this user is linked to.
     * A user may hold multiple staff records (many-to-many via staff_user).
     * Used by the AssessmentPolicy to check the user's clinical category.
     */
    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_user')
            ->withTimestamps();
    }

    // ─── Scopes ────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    // ─── Account lifecycle ─────────────────────────────

    public function deactivate(User $by, string $reason): void
    {
        $this->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivated_by' => $by->id,
            'deactivation_reason' => $reason,
        ]);
    }

    public function activate(): void
    {
        $this->update([
            'is_active' => true,
            'deactivated_at' => null,
            'deactivated_by' => null,
            'deactivation_reason' => null,
        ]);
    }
}