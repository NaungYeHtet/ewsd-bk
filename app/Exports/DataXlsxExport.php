<?php

namespace App\Exports;

use App\Exports\Sheets\CommentsSheet;
use App\Exports\Sheets\IdeasSheet;
use App\Models\AcademicDate;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataXlsxExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected ?AcademicDate $academicDate = null)
    {
        $this->academicDate = $academicDate ?? AcademicDate::orderBy('final_closure_date', 'desc')->first();
    }

    public function sheets(): array
    {
        return [
            new IdeasSheet($this->academicDate),
            new CommentsSheet($this->academicDate),
            // new DataSheet($this->academicDate, Comment::class),
        ];
    }
}
