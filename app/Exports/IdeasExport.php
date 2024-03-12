<?php

namespace App\Exports;

use App\Models\AcademicDate;
use App\Models\Idea;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class IdeasExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Idea::query();
    }

    public function headings(): array
    {
        return [
            'Academic Year',
            'Staff Name',
            'Staff Email',
            'Department Name',
            'Title',
            'Content',
            'Uploaded File',
            'Thumbs Up count',
            'Thumbs Down count',
            'Comments count',
            'Posted At',
        ];
    }

    /**
     * @param  Idea  $idea
     */
    public function map($idea): array
    {
        $staff = $idea->staff;
        $academicDate = AcademicDate::where('start_date', '<=', $idea->created_at)->where('closure_date', '>=', $idea->created_at)->first();

        return [
            $academicDate ? $academicDate->name : null,
            $staff->name,
            $staff->email,
            $staff->department->name,
            $idea->title,
            $idea->content,
            $idea->file ? url('/').Storage::url($idea->file) : null,
            $idea->reactions_count['THUMBS_UP'],
            $idea->reactions_count['THUMBS_DOWN'],
            $idea->comments_count,
            Date::dateTimeToExcel($idea->created_at),
        ];
    }
}
