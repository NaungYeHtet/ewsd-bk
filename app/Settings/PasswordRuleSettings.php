<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PasswordRuleSettings extends Settings
{
    public int $min;

    public int $max;

    public bool $letters;

    public bool $mixed_case;

    public bool $numbers;

    public bool $symbols;

    public static function group(): string
    {
        return 'password_rule';
    }

    public function getAll(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'letters' => $this->letters,
            'mixed_case' => $this->mixed_case,
            'numbers' => $this->numbers,
            'symbols' => $this->symbols,
        ];
    }
}
