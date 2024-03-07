<?php

namespace App\Http\Controllers;

use App\Data\CategoryData;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\PaginatedDataCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $categories = Category::where(function (Builder $query) use ($request) {
            $query->where('name', 'like', '%'.$request->search.'%');
        })->paginate($request->perpage ?? 5);

        return $this->responseSuccess([
            'results' => CategoryData::collect($categories, PaginatedDataCollection::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->responseSuccess([
            'result' => CategoryData::from($category),
        ], 'Category created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->responseSuccess([
            'result' => CategoryData::from($category),
        ], 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $idea = $category->ideas()->first();

        if ($idea) {
            return $this->responseError('Category cannot be deleted because it has ideas', code: 400);
        }

        $category->delete();

        return $this->responseSuccess(message: 'Category deleted successfully');
    }
}
