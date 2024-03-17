<?php

namespace App\Exports\Sheets;

use App\Models\Academic;
use App\Models\Idea;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class IdeasSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function __construct(protected Academic $academic)
    {
    }

    public function query()
    {
        return Idea::whereBetween('created_at', [$this->academic->start_date, $this->academic->closure_date])->orderBy('created_at', 'desc');
    }

    public function title(): string
    {
        return "Idea Data ({$this->academic->name})";
    }

    public function headings(): array
    {
        return [
            'Idea ID',
            'Academic Year',
            'Staff Name',
            'Staff Email',
            'Department Name',
            'Title',
            'Content',
            'Uploaded File',
            'Thumbs Up Count',
            'Thumbs Down Count',
            'Comments Count',
            'Views Count',
            'Posted At',
        ];
    }

    /**
     * @param  Idea  $idea
     */
    public function map($idea): array
    {
        $staff = $idea->staff;
        $academic = Academic::where('start_date', '<=', $idea->created_at)->where('closure_date', '>=', $idea->created_at)->first();

        return [
            $idea->slug,
            $academic ? $academic->name : null,
            $staff->name,
            $staff->email,
            $staff->department->name,
            $idea->title,
            $idea->content,
            $idea->file ? url('/').Storage::url($idea->file) : null,
            $idea->reactions_count['THUMBS_UP'],
            $idea->reactions_count['THUMBS_DOWN'],
            $idea->comments_count,
            $idea->views()->count(),
            Date::dateTimeToExcel($idea->created_at),
        ];
    }
}
