<?php

namespace App\Data;

use App\Models\AcademicDate;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/** @typescript */
class AcademicDateData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string|Optional $id,
        #[MapInputName('academic_year')]
        public string $academicYear,
        #[MapInputName('start_date'), WithCast(DateTimeInterfaceCast::class)]
        public Carbon $startDate,
        #[MapInputName('closure_date'), WithCast(DateTimeInterfaceCast::class)]
        public Carbon $closureDate,
        #[MapInputName('final_closure_date'), WithCast(DateTimeInterfaceCast::class)]
        public Carbon $finalClosureDate,
        public bool|Optional $isActive,
    ) {
    }

    public static function rules(): array
    {
        return [
            'academic_year' => ['required', 'string', 'min:5', 'max:255'],
            'start_date' => ['required', 'date'],
            'closure_date' => ['required', 'date', 'after:start_date'],
            'final_closure_date' => ['required', 'date', 'after:closure_date'],
        ];
    }

    public static function fromModel(AcademicDate $academicDate): self
    {
        return new self(
            $academicDate->uuid,
            $academicDate->academic_year,
            $academicDate->start_date,
            $academicDate->closure_date,
            $academicDate->final_closure_date,
            now()->between($academicDate->start_date, $academicDate->final_closure_date)
        );
    }
}
