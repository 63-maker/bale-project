<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintComment extends Model
{
    use HasFactory, HasUuids;

    public const VISIBILITY_PUBLIC = 'public';

    public const VISIBILITY_INTERNAL = 'internal';

    protected $fillable = [
        'complaint_id',
        'sender_id',
        'message',
        'visibility',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'sender_id');
    }
}
