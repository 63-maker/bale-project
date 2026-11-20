<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintStatusHistory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'complaint_status_history';

    protected $fillable = [
        'complaint_id',
        'previous_status',
        'new_status',
        'changed_by',
        'remarks',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'changed_by');
    }
}
