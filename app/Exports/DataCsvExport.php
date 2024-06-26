<?php

namespace App\Exports;

use App\Models\Academic;
use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class DataCsvExport implements FromView
{
    use Exportable;

    public function __construct(protected ?Academic $academic = null)
    {
        $this->academic = $academic ?? Academic::isActive()->first();
    }

    public function view(): View
    {
        return view('exports.all-data', [
            'ideas' => $this->academic->ideas()->orderBy('created_at', 'desc')->get(),
            'comments' => Comment::whereBetween('created_at', [$this->academic->start_date, $this->academic->final_closure_date])->orderBy('created_at', 'desc')->get(),
        ]);
    }
}
