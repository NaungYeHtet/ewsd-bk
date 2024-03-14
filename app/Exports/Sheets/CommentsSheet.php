<?php

namespace App\Exports\Sheets;

use App\Models\AcademicDate;
use App\Models\Comment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CommentsSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function __construct(protected AcademicDate $academicDate)
    {
    }

    public function query()
    {
        return Comment::whereBetween('created_at', [$this->academicDate->start_date, $this->academicDate->final_closure_date])->orderBy('created_at', 'desc');
    }

    public function title(): string
    {
        return "Idea Comment Data ({$this->academicDate->academic_year})";
    }

    public function headings(): array
    {
        return [
            'Comment ID',
            'Idea ID',
            'Staff Email',
            'Department Name',
            'Content',
            'Commented At',
        ];
    }

    /**
     * @param  Comment  $comment
     */
    public function map($comment): array
    {
        $staff = $comment->staff;

        return [
            $comment->uuid,
            $comment->commentable->slug,
            $staff->email,
            $staff->department->name,
            $comment->content,
            Date::dateTimeToExcel($comment->created_at),
        ];
    }
}
