<?php

namespace App\Data\Statistics;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/** @typescript */
class Stat extends Data
{
    public function __construct(
        public string $label,
        public string $value,
        public string|Optional $path,
    ) {
    }
}
