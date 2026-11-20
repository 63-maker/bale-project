<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintAssignment extends Model
{
    use HasFactory, HasUuids;

    public const METHOD_MANUAL = 'manual';

    public const METHOD_AUTOMATIC = 'automatic';

    protected $fillable = [
        'complaint_id',
        'management_id',
        'assigned_by',
        'assignment_method',
        'is_active',
        'assigned_at',
        'unassigned_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'assigned_at' => 'datetime',
            'unassigned_at' => 'datetime',
        ];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function management(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'management_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'assigned_by');
    }
}
