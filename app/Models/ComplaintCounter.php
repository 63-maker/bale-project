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
     */
    public static function getNextComplaintNumber(int $year): string
    {
        return DB::transaction(function () use ($year) {
            $counter = static::where('year', $year)->lockForUpdate()->first();

            if (! $counter) {
                $counter = static::create([
                    'year' => $year,
                    'last_number' => 1,
                ]);
                $num = 1;
            } else {
                $counter->increment('last_number');
                $num = $counter->last_number;
            }

            return sprintf('CCMS-%d-%06d', $year, $num);
        });
    }
}
