<?php

namespace App\Exports;

use App\Models\AcademicDate;
use App\Models\Comment;
use App\Models\Idea;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class DataCsvExport implements FromView
{
    use Exportable;

    public function __construct(protected ?AcademicDate $academicDate = null)
    {
        $this->academicDate = $academicDate ?? AcademicDate::orderBy('final_closure_date', 'desc')->first();
    }

    public function view(): View
    {
        return view('exports.all-data', [
            'ideas' => Idea::whereBetween('created_at', [$this->academicDate->start_date, $this->academicDate->closure_date])->orderBy('created_at', 'desc')->get(),
            'comments' => Comment::whereBetween('created_at', [$this->academicDate->start_date, $this->academicDate->final_closure_date])->orderBy('created_at', 'desc')->get(),
        ]);
    }
}
