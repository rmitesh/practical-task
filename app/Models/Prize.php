<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prize extends Model
{
    public const MAX_PROBABILITY_LIMIT = 100.00;

    protected $fillable = [
        'title',
        'probability',
        'awarded',
    ];

    protected $casts = [
        'probability' => 'double',
    ];

    public static function rndRGBColorCode() { 
        return 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
    }

    public static function getTotalProbability(): float
    {
        return cache()->remember('totalProbability', now()->addDays(1), function() {
            return floatval(self::sum('probability'));
        });;
    }

    public static function nextPrize(): void
    {
        $prizes = self::select([
            'id', 'title', 'probability', 'awarded',
        ])->get();

        $totalProbability = $prizes->sum('probability');

        $randomProbability = rand(1, $totalProbability);

        $cumulativeProbability = 0;

        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize->probability;
            if ( $randomProbability <= $cumulativeProbability ) {
                $prize->increment('awarded');
            }
        }
    }
}
