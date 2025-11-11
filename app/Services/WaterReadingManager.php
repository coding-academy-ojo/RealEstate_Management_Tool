<?php

namespace App\Services;

use App\Models\WaterService;

class WaterReadingManager
{
    /**
     * Recalculate consumption values for all readings of the given service.
     */
    public static function recalculate(WaterService $waterService): void
    {
        $readings = $waterService->readings()
            ->orderBy('reading_date')
            ->orderBy('id')
            ->get();

        $previous = 0.0;
        $isFirst = true;

        foreach ($readings as $reading) {
            $current = (float) ($reading->current_reading ?? 0);
            $consumption = $isFirst
                ? $current
                : max(0, round($current - $previous, 2));

            $reading->forceFill([
                'consumption_value' => $consumption,
            ])->saveQuietly();

            $previous = $current;
            $isFirst = false;
        }
    }
}
