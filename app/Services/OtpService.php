<?php

namespace App\Services;

use App\Enums\OtpAction;
use App\Exceptions\InvalidOtpException;
use App\Models\OneTimePasscode;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function __construct(private string $identifier)
    {
    }

    public function generate(OtpAction $action): string
    {
        $this->delete();
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        OneTimePasscode::create([
            'identifier' => $this->identifier,
            'code' => bcrypt($code),
            'action' => $action->value,
            'expires_at' => now()->addMinutes($action->getLifetime()),
        ]);

        return $code;
    }

    public function verify(OtpAction $action, $code): void
    {
        $requested = OneTimePasscode::where('identifier', $this->identifier)->where('expires_at', '>', now())->where('action', $action->value)->first();

        if (! $requested) {
            throw new InvalidOtpException();
        }

        if (!Hash::check($code, $requested->code)) {
            throw new InvalidOtpException();
        }

        $this->delete();
    }

    public function delete()
    {
        OneTimePasscode::where('identifier', $this->identifier)->delete();
    }
}
