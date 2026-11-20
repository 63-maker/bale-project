<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintAttachment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'complaint_id',
        'uploaded_by',
        'storage_path',
        'original_filename',
        'mime_type',
        'file_size',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'uploaded_by');
    }
}
