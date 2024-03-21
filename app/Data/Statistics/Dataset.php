<?php

namespace App\Data\Statistics;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/** @typescript */
class Dataset extends Data
{
    public function __construct(
        public string $label,
        public array $data,
        public bool|Optional $fill,
        public string|Optional $borderColor,
        public array|Optional $backgroundColor,
    ) {
    }
}
