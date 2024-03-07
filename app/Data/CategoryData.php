<?php

namespace App\Data;

use App\Models\Idea;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryData extends Data
{
    public function __construct(
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $name,
        public string $slug,
    ) {
    }
}
