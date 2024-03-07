<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuids
{
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
