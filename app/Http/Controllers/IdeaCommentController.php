<?php

namespace App\Http\Controllers;

use App\Data\CommentData;
use App\Events\CommentSubmitted;
use App\Exports\CommentsExport;
use App\Http\Requests\ExportRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ReportRequest;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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

    public function store(Idea $idea, CommentData $data)
    {
        $comment = DB::transaction(function () use ($idea, $data) {
            $staff = Staff::find(auth()->id());

            $idea->views()->firstOrCreate([
                'staff_id' => $staff->id(),
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
    
    public function report(Idea $idea, ReportRequest $request, Comment $comment){
        $staff = Staff::find(auth()->id());

        if ($comment->reports()->where('staff_id', $staff->id)->exists()) {
            return $this->responseError('Comment already reported', code: 400);
        }

        DB::transaction(function () use ($staff, $request, $comment) {
            $comment->reports()->create([
                'staff_id' => $staff->id,
                'reason' => $request->reason,
            ]);
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
