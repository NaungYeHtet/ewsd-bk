<?php

namespace App\Data;

use App\Models\Staff;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

class StaffData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string|Optional $id,
        public string $name,
        public string $email,
        public string|Optional $username,
        public string|Optional|null $avatar,
        public Lazy|string $role,
    ) {
    }

    public static function fromModel(Staff $staff): self
    {
        return new self(
            $staff->uuid,
            $staff->name,
            $staff->email,
            $staff->username,
            $staff->avatar,
            Lazy::create(fn () => RoleData::from($staff->roles()->first())->name)
        );
    }
}
