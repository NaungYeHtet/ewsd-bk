<?php

namespace App\Exports;

use App\Exports\Sheets\CommentsSheet;
use App\Exports\Sheets\IdeasSheet;
use App\Models\Academic;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataXlsxExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected ?Academic $academic = null)
    {
        $this->academic = $academic ?? Academic::orderBy('final_closure_date', 'desc')->first();
    }

    public function sheets(): array
    {
        return [
            new IdeasSheet($this->academic),
            new CommentsSheet($this->academic),
            // new DataSheet($this->academic, Comment::class),
        ];
    }
}
