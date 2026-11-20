<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Complaint extends Model
{
    use HasFactory, HasUuids;

    public const STATUS_SUBMITTED = 'SUBMITTED';

    public const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';

    public const STATUS_ASSIGNED = 'ASSIGNED';

    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';

    public const STATUS_RESOLUTION_SUBMITTED = 'RESOLUTION_SUBMITTED';

    public const STATUS_VERIFICATION_REJECTED = 'VERIFICATION_REJECTED';

    public const STATUS_VERIFIED_FOR_CLOSURE = 'VERIFIED_FOR_CLOSURE';

    public const STATUS_CLOSED = 'CLOSED';

    public const STATUS_REOPENED = 'REOPENED';

    public const STATUS_REJECTED = 'REJECTED';

    public const PRIORITY_LOW = 'LOW';

    public const PRIORITY_MEDIUM = 'MEDIUM';

    public const PRIORITY_HIGH = 'HIGH';

    public const PRIORITY_CRITICAL = 'CRITICAL';

    protected $fillable = [
        'complaint_number',
        'student_id',
        'category_id',
        'department_id',
        'subject',
        'description',
        'priority',
        'status',
        'resolution_description',
        'resolution_submitted_at',
        'verified_by',
        'verified_at',
        'verification_remarks',
        'closed_by',
        'closed_at',
        'rejection_reason',
        'reopened_reason',
    ];

    protected function casts(): array
    {
        return [
            'resolution_submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'student_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'verified_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'closed_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ComplaintAssignment::class)->orderByDesc('created_at');
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(ComplaintAssignment::class)->where('is_active', true);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ComplaintComment::class)->orderBy('created_at');
    }

    public function publicComments(): HasMany
    {
        return $this->hasMany(ComplaintComment::class)->where('visibility', 'public')->orderBy('created_at');
    }

    public function internalComments(): HasMany
    {
        return $this->hasMany(ComplaintComment::class)->where('visibility', 'internal')->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class)->orderBy('created_at');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ComplaintStatusHistory::class)->orderBy('created_at');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ComplaintVerification::class)->orderByDesc('created_at');
    }

    public function isAssignedTo(string $managementId): bool
    {
        return $this->assignments()
            ->where('management_id', $managementId)
            ->where('is_active', true)
            ->exists();
    }

    public function canBeReopenedBy(Profile $user): bool
    {
        return $user->id === $this->student_id && $this->status === self::STATUS_CLOSED;
    }
}
