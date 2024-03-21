<?php

namespace App\Data\Statistics;

use App\Enums\ChartType;
use Spatie\LaravelData\Data;

/** @typescript */
class Chart extends Data
{
    public function __construct(
        public string $label,
        public ChartType $type,
        public ChartData $data,
    ) {
    }
}
