<?php

namespace App\Support;

final class Duree
{
    /** 90 → "1 h 30", 45 → "45 min", 120 → "2 h". */
    public static function format(?int $minutes): string
    {
        if ($minutes === null) {
            return '—';
        }
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return match (true) {
            $h === 0 => "{$m} min",
            $m === 0 => "{$h} h",
            default => sprintf('%d h %02d', $h, $m),
        };
    }
}
