<?php

use App\Services\PositionCalculator;

test('calculates the next position when no existing position exists', function () {
    $calculator = new PositionCalculator();

    expect($calculator->nextPosition(null))->toBe(0);
});

test('calculates the next position after the current maximum', function () {
    $calculator = new PositionCalculator();

    expect($calculator->nextPosition(0))->toBe(1);
    expect($calculator->nextPosition(4))->toBe(5);
    expect($calculator->nextPosition(10))->toBe(11);
});