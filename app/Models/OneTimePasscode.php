<?php

namespace App\Models;

use App\Enums\OtpAction;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimePasscode extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'identifier',
        'code',
        'action',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'action' => OtpAction::class,
    ];
}
