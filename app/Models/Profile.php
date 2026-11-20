<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Profile extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    public const ROLE_STUDENT = 'student';

    public const ROLE_MANAGEMENT = 'management';

    public const ROLE_ADMIN = 'admin';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    protected $table = 'profiles';

    protected $fillable = [
        'register_number',
        'employee_id',
        'full_name',
        'email',
        'password',
        'password_hash',
        'role',
        'department_id',
        'study_year',
        'account_status',
        'must_change_password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'study_year' => 'integer',
            'must_change_password' => 'boolean',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password_hash'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'student_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ComplaintAssignment::class, 'management_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ComplaintComment::class, 'sender_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isManagement(): bool
    {
        return $this->role === self::ROLE_MANAGEMENT;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isActive(): bool
    {
        return $this->account_status === self::STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->account_status === self::STATUS_SUSPENDED;
    }
}
