<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicDate extends Model
{
    use HasFactory, HasUuids;

    public $fillable = ['academic_year', 'start_date', 'closure_date', 'final_closure_date'];

    protected $primaryKey = 'uuid';

    protected $casts = [
        'start_date' => 'date',
        'closure_date' => 'date',
        'final_closure_date' => 'date',
    ];

    public static function isDateBetweenStartAndClosureDate($date = null): bool
    {
        $date = $date ?? now();

        return AcademicDate::where('start_date', '<=', $date)->where('closure_date', '>=', $date)->exists();
    }

    public static function isDateBetweenStartAndFinalClosureDate($date = null): bool
    {
        $date = $date ?? now();

        return AcademicDate::where('start_date', '<=', $date)->where('final_closure_date', '>=', $date)->exists();
    }
}
