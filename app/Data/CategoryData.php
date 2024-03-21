<?php

namespace App\Data;

use App\Models\Category;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

/** @typescript */
class CategoryData extends Data
{
    public function __construct(
        public string|Optional $slug,
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $name,
        public Lazy|int|Optional $ideasCount,
        public string|Optional $createdAt
    ) {
    }

    public static function fromModel(Category $category): self
    {
        return new self(
            $category->slug,
            $category->name,
            Lazy::create(fn () => $category->ideas()->count()),
            $category->created_at->format('Y-m-d H:i:s'),
        );
    }
}
