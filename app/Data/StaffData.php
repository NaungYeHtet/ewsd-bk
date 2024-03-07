<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class StaffData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string $id,
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $name,
        #[Rule(['required', 'string', 'email'])]
        public string $email,
        #[Rule(['string', 'max:255'])]
        public string $username,
    ) {
    }
}
