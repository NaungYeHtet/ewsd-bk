<?php

namespace App\Http\Controllers;

use App\Data\CommentData;
use App\Events\CommentSubmitted;
use App\Http\Requests\IndexRequest;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
