<?php

namespace App\Services;

class PositionCalculator
{
    public function nextPosition(?int $maxPosition): int
    {
        return ($maxPosition ?? -1) + 1;
    }
}
