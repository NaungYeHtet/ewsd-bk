<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/** @typescript */
class CategoryData extends Data
{
    public function __construct(
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $name,
        public string|Optional $slug,
    ) {
    }
}
