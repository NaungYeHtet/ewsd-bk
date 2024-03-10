<?php

namespace App\Http\Controllers;

use App\Data\IdeaData;
use App\Enums\ReactionType;
use App\Events\IdeaSubmitted;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\PaginatedDataCollection;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $request->validate([
            'sort' => ['string', 'in:popular,views'],
        ]);

        $ideas = Idea::query()
            ->when($request->has('search'), function (Builder $query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('content', 'like', '%'.$request->search.'%');
            })
            ->when($request->has('sort'), function (Builder $query) use ($request) {
                if ($request->sort == 'popular') {
                    $query->orderBy(DB::raw('JSON_EXTRACT(reactions_count, "$.THUMBS_UP") - JSON_EXTRACT(reactions_count, "$.THUMBS_DOWN")'), 'desc');
                }

                if ($request->sort == 'views') {
                    $query->orderBy('views_count', 'desc');
                }
            })
            ->when(! $request->has('sort'), function (Builder $query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($request->perpage ?? 5);

        return $this->responseSuccess([
            'results' => IdeaData::collect($ideas, PaginatedDataCollection::class)->include('staff'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request)
    {
        $idea = DB::transaction(function () use ($request) {
            $staff = Staff::find(auth()->id());

            $fileName = '';

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $ext = $file->extension();
                $fileName = $file->storeAs('/images/files', uniqid().'.'.$ext, ['disk' => 'public']);
            }

            $idea = $staff->ideas()->create([
                'title' => $request->title,
                'content' => $request->content,
                'file' => $fileName,
                'reactions_count' => ReactionType::getDefaults(),
                'is_anonymous' => $request->is_anonymous,
            ]);

            $idea->refresh();
            $category = Category::findBySlug($request->category);

            $idea->categories()->attach($category->id);
            $idea->refresh();

            // IdeaSubmitted::dispatch($idea);
            return $idea;
        });

        return $this->responseSuccess([
            'result' => IdeaData::from($idea),
        ], 'Idea posted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdeaRequest $request, Idea $idea)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea)
    {
        //
    }
}
