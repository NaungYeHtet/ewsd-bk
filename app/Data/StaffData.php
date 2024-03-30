<?php

namespace App\Data;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

/** @typescript */
class StaffData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string|Optional $id,
        public string $name,
        public string $email,
        public string|Optional $username,
        public string|Optional|null $avatar,
        #[MapInputName('disabled_at')]
        public ?Carbon $disabledAt,
        #[MapInputName('last_logged_in_at')]
        public ?string $lastLoggedInAt,
        public Lazy|string $role,
        public Lazy|DepartmentData $department,
        public int $unreadNotiCount,
    ) {
    }

    public static function fromModel(Staff $staff): self
    {
        return new self(
            $staff->uuid,
            $staff->name,
            $staff->email,
            $staff->username,
            $staff->avatar ? url('/').Storage::url($staff->avatar) : url('/').Storage::url('public/images/default-avatar.png'),
            $staff->disabled_at,
            $staff->last_logged_in_at ? $staff->last_logged_in_at->format('Y-m-d H:i:s') : null,
            Lazy::create(fn () => RoleData::from($staff->roles()->first())->name),
            Lazy::create(fn () => DepartmentData::from($staff->department))->include('staffsCount'),
            $staff->unreadNotifications()->count(),
        );
    }
}
