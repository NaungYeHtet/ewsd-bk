<?php

namespace App\Data;

use Spatie\LaravelData\Data;

/** @typescript */
class FileType extends Data
{
    public function __construct(
        public string $url,
        public string $type,
    ) {
    }
}
