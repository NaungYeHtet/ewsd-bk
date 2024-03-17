<?php

namespace App\Http\Controllers;

use App\Data\IdeaData;
use App\Enums\ReactionType;
use App\Events\IdeaSubmitted;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ReactIdeaRequest;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Staff;
use App\Traits\Zippable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\PaginatedDataCollection;

class IdeaController extends Controller
{
    use Zippable;

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $request->validate([
            'sort' => ['string', 'in:popular,views'],
            'staff' => ['exists:staffs,uuid'],
            'category' => ['exists:categories,slug'],
        ]);

        $ideas = Idea::query()
            ->where(function (Builder $query) use ($request) {
                if ($request->has('search')) {
                    $query->where('title', 'like', '%'.$request->search.'%')
                        ->orWhere('content', 'like', '%'.$request->search.'%');
                }
                if ($request->has('staff')) {
                    $staff = Staff::where('uuid', $request->staff)->first();
                    $query->where('is_anonymous', 0)->where('staff_id', $staff->id);
                }

                if ($request->has('category')) {
                    $category = Category::findBySlug($request->category);
                    $query->whereRelation('categories', 'category_id', $category->id);
                }
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
            'results' => IdeaData::collect($ideas, PaginatedDataCollection::class)->include('staff', 'viewsCount', 'category'),
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

            IdeaSubmitted::dispatch($idea);

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
        $idea->views()->firstOrCreate([
            'staff_id' => auth()->id(),
        ]);

        return $this->responseSuccess([
            'results' => IdeaData::from($idea)->include('staff', 'viewsCount', 'category'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdeaRequest $request, Idea $idea)
    {
        $idea = DB::transaction(function () use ($request, $idea) {
            $fileName = $idea->file;

            if ($request->hasFile('file')) {
                if ($idea->file) {
                    Storage::disk('public')->delete($idea->file);
                }

                $file = $request->file('file');
                $ext = $file->extension();
                $fileName = $file->storeAs('/images/files', uniqid().'.'.$ext, ['disk' => 'public']);
            }

            $idea->update([
                'title' => $request->title,
                'content' => $request->content,
                'file' => $fileName,
                'is_anonymous' => $request->is_anonymous,
            ]);

            $idea->refresh();
            $category = Category::findBySlug($request->category);

            $idea->categories()->sync($category->id);
            $idea->refresh();

            // IdeaUpdated::dispatch($idea);

            return $idea;
        });

        return $this->responseSuccess([
            'result' => IdeaData::from($idea),
        ], 'Idea updated successfully');
    }

    public function report(Idea $idea, ReportRequest $request)
    {
        $staff = Staff::find(auth()->id());

        if ($idea->reports()->where('staff_id', $staff->id)->exists()) {
            return $this->responseError('Idea already reported', code: 400);
        }

        DB::transaction(function () use ($staff, $request, $idea) {
            $idea->views()->firstOrCreate([
                'staff_id' => $staff->id,
            ]);

            $idea->reports()->create([
                'staff_id' => $staff->id,
                'reason' => $request->reason,
            ]);
        });

        return $this->responseSuccess([], 'Idea reported successfully');
    }

    public function react(Idea $idea, ReactIdeaRequest $request)
    {
        DB::transaction(function () use ($request, $idea) {
            $exist = $idea->reactions()->where('staff_id', auth()->id())->where('type', $request->type)->first();

            $idea->views()->firstOrCreate([
                'staff_id' => auth()->id(),
            ]);

            $exist ? $exist->delete() : $idea->reactions()->create([
                'staff_id' => auth()->id(),
                'type' => $request->type,
            ]);

            $currentReactionsCount = $idea->reactions_count;

            $currentReactionsCount[$request->type] = $exist ? $currentReactionsCount[$request->type] - 1 : $currentReactionsCount[$request->type] + 1;
            $idea->reactions_count = $currentReactionsCount;
            $idea->save();
        });

        return $this->responseSuccess([
            'result' => IdeaData::from($idea),
        ], 'React successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea)
    {
        DB::transaction(function () use ($idea) {
            if ($idea->file) {
                Storage::disk('public')->delete($idea->file);
            }

            $idea->categories()->detach();
            $idea->comments()->delete();
            $idea->reactions()->delete();
            $idea->views()->delete();
            $idea->delete();
        });

        return $this->responseSuccess([], 'Idea deleted successfully');
    }
}
