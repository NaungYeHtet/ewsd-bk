<?php

namespace App\Http\Controllers;

use App\Data\IdeaData;
use App\Http\Requests\IndexIdeaRequest;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Http\Resources\IdeaResource;
use App\Models\Idea;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexIdeaRequest $request)
    {
        $ideas = Idea::where(function (Builder $query) use ($request) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('content', 'like', '%'.$request->search.'%');
        })->paginate($request->perpage ?? 5);

        return $this->responseSuccess([
            'results' => IdeaData::collect($ideas, PaginatedDataCollection::class)->include('staff'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request)
    {
        //
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
