<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComplaintCounter extends Model
{
    protected $primaryKey = 'year';

    public $incrementing = false;

    protected $keyType = 'integer';

    protected $fillable = [
        'year',
        'last_number',
    ];

    /**
     * Atomically generate next sequential complaint number using DB row lock
     * Format: CCMS-YYYY-000001
     * Guaranteed to be collision-free and resilient against pre-seeded or manual records.
     */
    public static function getNextComplaintNumber(int $year): string
    {
        return DB::transaction(function () use ($year) {
            $counter = static::where('year', $year)->lockForUpdate()->first();

            // Find current maximum sequence in complaints table for this year
            $prefix = sprintf('CCMS-%d-', $year);
            $latestComplaint = Complaint::where('complaint_number', 'like', $prefix.'%')
                ->orderByDesc('complaint_number')
                ->first();

            $maxExisting = 0;
            if ($latestComplaint) {
                $parts = explode('-', $latestComplaint->complaint_number);
                $maxExisting = (int) end($parts);
            }

            if (! $counter) {
                $num = max($maxExisting, 0) + 1;
                $counter = static::create([
                    'year' => $year,
                    'last_number' => $num,
                ]);
            } else {
                $num = max($counter->last_number, $maxExisting) + 1;
                $counter->update(['last_number' => $num]);
            }

            // Ensure collision-free guarantee even if gaps or race conditions exist
            while (Complaint::where('complaint_number', sprintf('CCMS-%d-%06d', $year, $num))->exists()) {
                $num++;
                $counter->update(['last_number' => $num]);
            }

            return sprintf('CCMS-%d-%06d', $year, $num);
        });
    }
}
