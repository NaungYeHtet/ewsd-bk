<?php

namespace App\Data;

use App\Models\Department;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

/** @typescript */
class DepartmentData extends Data
{
    public function __construct(
        public string|Optional $slug,
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $name,
        public Lazy|int $staffsCount
    ) {
    }

    public static function fromModel(Department $department): self
    {
        return new self(
            $department->slug,
            $department->name,
            Lazy::create(fn () => $department->staffs()->count()),
        );
    }
}
