<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'division_id', 'is_active', 'jabatan', 'no_hp', 'photo', 'status', 'rejection_reason'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_KARYAWAN = 'karyawan';
    public const ROLE_MANAGER  = 'manager';
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
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function dailyPlans(): HasMany
    {
        return $this->hasMany(DailyPlan::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isKaryawan(): bool
    {
        return $this->role === self::ROLE_KARYAWAN;
    }

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

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN   => 'Admin',
            self::ROLE_MANAGER => 'Manager',
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
