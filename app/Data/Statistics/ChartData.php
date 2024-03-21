<?php

namespace App\Data\Statistics;

use Spatie\LaravelData\Data;

/** @typescript */
class ChartData extends Data
{
    public function __construct(
        public array $labels,
        /** @var array<Dataset> */
        public array $datasets,
    ) {
    }
}
