<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintVerification extends Model
{
    use HasFactory, HasUuids;

    public const DECISION_APPROVED = 'approved';

    public const DECISION_REJECTED = 'rejected';

    protected $fillable = [
        'complaint_id',
        'management_id',
        'admin_id',
        'decision',
        'remarks',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function management(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'management_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'admin_id');
    }
}
