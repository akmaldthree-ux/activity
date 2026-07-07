<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'division_id', 'reports_to', 'is_active', 'jabatan', 'no_hp', 'photo', 'status', 'rejection_reason'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_KARYAWAN = 'karyawan';
    public const ROLE_LEADER   = 'leader';
    public const ROLE_MANAGER  = 'manager';
    public const ROLE_DIREKSI  = 'direksi';
    public const ROLE_ADMIN    = 'admin';

    public const STATUS_PENDING           = 'pending';
    public const STATUS_MANAGER_APPROVED  = 'manager_approved';
    public const STATUS_ACTIVE            = 'active';
    public const STATUS_REJECTED          = 'rejected';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'reports_to'        => 'integer',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reports_to');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'reports_to');
    }

    public function dailyPlans(): HasMany
    {
        return $this->hasMany(DailyPlan::class);
    }

    // ── Role checks ──────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isDireksi(): bool
    {
        return $this->role === self::ROLE_DIREKSI;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isLeader(): bool
    {
        return $this->role === self::ROLE_LEADER;
    }

    public function isKaryawan(): bool
    {
        return $this->role === self::ROLE_KARYAWAN;
    }

    /** True if this user must fill a daily plan. */
    public function canFillPlan(): bool
    {
        return in_array($this->role, [self::ROLE_KARYAWAN, self::ROLE_LEADER, self::ROLE_MANAGER]);
    }

    /** True if this user can monitor subordinates. */
    public function canMonitor(): bool
    {
        return in_array($this->role, [self::ROLE_LEADER, self::ROLE_MANAGER, self::ROLE_DIREKSI, self::ROLE_ADMIN]);
    }

    // ── Status checks ────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isManagerApproved(): bool
    {
        return $this->status === self::STATUS_MANAGER_APPROVED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // ── Labels ───────────────────────────────────────────────────────────

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN   => 'Admin',
            self::ROLE_DIREKSI => 'Direksi',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_LEADER  => 'Leader',
            default            => 'Karyawan',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING          => 'Menunggu Persetujuan Manager',
            self::STATUS_MANAGER_APPROVED => 'Menunggu Persetujuan Admin',
            self::STATUS_REJECTED         => 'Ditolak',
            default                       => 'Aktif',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING          => 'bg-warning text-dark',
            self::STATUS_MANAGER_APPROVED => 'bg-info text-dark',
            self::STATUS_REJECTED         => 'bg-danger',
            default                       => 'bg-success',
        };
    }
}
