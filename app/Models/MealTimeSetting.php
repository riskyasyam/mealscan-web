<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MealTimeSetting extends Model
{
    protected $fillable = [
        'meal_type',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time'   => 'string',
        'is_active' => 'boolean',
    ];

    public static function getCurrentMealType(): ?string
    {
        $now = Carbon::now();

        $settings = self::where('is_active', true)->get();

        foreach ($settings as $setting) {
            $start = Carbon::parse($setting->start_time);
            $end   = Carbon::parse($setting->end_time);

            if ($now->between($start, $end)) {
                return $setting->meal_type;
            }
        }

        return null;
    }

    
    public function isActiveNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now()->format('H:i:s');

        return $now >= $this->start_time && $now <= $this->end_time;
    }
}
