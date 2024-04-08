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
        public bool|Optional $isIdeasHidden,
        public bool|Optional $isCommentsHidden,
    ) {
    }

    public static function fromModel(Staff $staff): self
    {
        $avatar = '';

        $fileName = $staff->avatar ?? 'images/default-avatar.png';

        if (config('filesystems.default') == 's3') {
            $avatar = Storage::temporaryUrl($fileName, now()->addMinutes(30));
        } else {
            $avatar = url('/').Storage::url('public/'.$fileName);
        }

        return new self(
            $staff->uuid,
            $staff->name,
            $staff->email,
            $staff->username,
            $avatar,
            $staff->disabled_at,
            $staff->last_logged_in_at ? $staff->last_logged_in_at->format('Y-m-d H:i:s') : null,
            // '2024-02-26 13:42:33',
            Lazy::create(fn () => RoleData::from($staff->roles()->first())->name),
            Lazy::create(fn () => DepartmentData::fromModel($staff->department))->include('staffsCount'),
            $staff->unreadNotifications()->count(),
            (bool) $staff->ideas_hidden_at,
            (bool) $staff->comments_hidden_at,
        );
    }
}
