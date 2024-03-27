<?php

namespace App\Enums;

enum OtpAction: string
{
    case EMAIL_VERIFICATION = 'email_verification';

    public function getLifeTime(): int
    {
        return $this->value === self::EMAIL_VERIFICATION ? 60 : 30;
    }
}
