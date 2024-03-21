<?php

namespace App\Http\Controllers;

use App\Data\CommentData;
use App\Events\CommentSubmitted;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ReactIdeaRequest;
use App\Http\Requests\ReportRequest;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\LaravelData\PaginatedDataCollection;

class IdeaCommentController extends Controller
{
    public function index(Idea $idea, IndexRequest $request)
    {
        $ideas = $idea->comments()
            ->when($request->has('search'), function (Builder $query) use ($request) {
                $query->orWhere('content', 'like', '%'.$request->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->perpage ?? 5);

        return $this->responseSuccess([
            'results' => CommentData::collect($ideas, PaginatedDataCollection::class)->include('staff'),
        ]);
    }

    public function react(Idea $idea, Comment $comment, ReactIdeaRequest $request)
    {
        DB::transaction(function () use ($request, $comment) {
            $exist = $comment->reactions()->where('staff_id', auth()->id())->where('type', $request->type)->first();

            $exist ? $exist->delete() : $comment->reactions()->create([
                'staff_id' => auth()->id(),
                'type' => $request->type,
            ]);
        });

        return $this->responseSuccess([
            'result' => CommentData::from($comment),
        ], 'React successfully');
    }

    public function store(Idea $idea, CommentData $data)
    {
        $comment = DB::transaction(function () use ($idea, $data) {
            $staff = Staff::find(auth()->id());

            $idea->views()->firstOrCreate([
                'staff_id' => $staff->id,
            ]);

            $comment = $idea->comments()->create([
                'content' => $data->content,
                'is_anonymous' => $data->isAnonymous,
                'staff_id' => $staff->id,
            ]);

            $comment->refresh();

            CommentSubmitted::dispatch($comment);

            return $comment;
        });

        return $this->responseSuccess([
            'result' => CommentData::from($comment)->include('staff'),
        ], 'Comment submitted successfully');
    }

    public function report(Idea $idea, ReportRequest $request, Comment $comment)
    {
        $staff = Staff::find(auth()->id());

        if ($comment->reports()->where('staff_id', $staff->id)->exists()) {
            return $this->responseError('Comment already reported', code: 400);
        }

        DB::transaction(function () use ($staff, $request, $comment) {
            $report = $comment->reports()->create([
                'staff_id' => $staff->id,
                'reason' => $request->reason,
            ]);

            Notification::send(Staff::whereRelation('roles', 'name', '=', 'Admin')->get(), new \App\Notifications\ReportSubmitted($report->refresh()));
        });

        return $this->responseSuccess([], 'Comment reported successfully');
    }

    public function update(Idea $idea, Comment $comment, CommentData $data)
    {
        $comment = DB::transaction(function () use ($comment, $data) {
            $comment->update([
                'content' => $data->content,
            ]);

            $comment->refresh();

            // CommentUpdated::dispatch($comment);
            return $comment;
        });

        return $this->responseSuccess([
            'result' => CommentData::from($comment)->include('staff'),
        ], 'Comment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea, Comment $comment)
    {
        $comment->delete();

        return $this->responseSuccess([], 'Comment deleted successfully');
    }
}
