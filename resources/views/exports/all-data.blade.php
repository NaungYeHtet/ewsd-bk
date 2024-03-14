<?php
use App\Models\AcademicDate;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date;

?>
<table>
    <thead>
        <tr>
            <th>Idea Data</th>
        </tr>
        <tr>
            <th>Idea ID</th>
            <th>Academic Year</th>
            <th>Staff Name</th>
            <th>Staff Email</th>
            <th>Department Name</th>
            <th>Title</th>
            <th>Content</th>
            <th>Uploaded File</th>
            <th>Thumbs Up Count</th>
            <th>Thumbs Down Count</th>
            <th>Comments Count</th>
            <th>Views Count</th>
            <th>Posted At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ideas as $idea)
            <?php
            $staff = $idea->staff;
            $academicDate = AcademicDate::where('start_date', '<=', $idea->created_at)
                ->where('closure_date', '>=', $idea->created_at)
                ->first();
            $fileUrl = $idea->file ? url('/') . Storage::url($idea->file) : null;
            ?>
            <tr>
                <td>{{ $idea->slug }}</td>
                <td>{{ $academicDate ? $academicDate->academic_year : '' }}</td>
                <td>{{ $staff->name }}</td>
                <td>{{ $staff->email }}</td>
                <td>{{ $staff->department->name }}</td>
                <td>{{ $idea->title }}</td>
                <td>{{ $fileUrl }}</td>
                <td>{{ $idea->reactions_count['THUMBS_UP'] }}</td>
                <td>{{ $idea->reactions_count['THUMBS_DOWN'] }}</td>
                <td>{{ $idea->comments_count }}</td>
                <td>{{ $idea->views()->count() }}</td>
                <td>{{ Date::dateTimeToExcel($idea->created_at) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>Idea Comment Data</th>
        </tr>
        <tr>
            <th>Comment ID</th>
            <th>Idea ID</th>
            <th>Staff Email</th>
            <th>Department Name</th>
            <th>Content</th>
            <th>Commented At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($comments as $comment)
            <?php
            $staff = $comment->staff;
            ?>
            <tr>
                <td>{{ $comment->uuid }}</td>
                <td>{{ $comment->commentable->slug }}</td>
                <td>{{ $staff->email }}</td>
                <td>{{ $staff->department->name }}</td>
                <td>{{ $comment->content }}</td>
                <td>{{ Date::dateTimeToExcel($comment->created_at) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
