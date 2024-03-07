<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSetting extends Settings
{
    public string $closure_date;

    public string $final_closure_date;

    public static function group(): string
    {
        return 'general';
    }
}
