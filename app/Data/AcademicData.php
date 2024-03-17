<?php

namespace App\Data;

use App\Models\Academic;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/** @typescript */
class AcademicData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string|Optional $id,
        public string $name,
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
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'start_date' => ['required', 'date'],
            'closure_date' => ['required', 'date', 'after:start_date'],
            'final_closure_date' => ['required', 'date', 'after:closure_date'],
        ];
    }

    public static function fromModel(Academic $academic): self
    {
        return new self(
            $academic->uuid,
            $academic->name,
            $academic->start_date,
            $academic->closure_date,
            $academic->final_closure_date,
            now()->between($academic->start_date, $academic->final_closure_date)
        );
    }
}
