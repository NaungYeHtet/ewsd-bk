<?php

namespace App\Http\Controllers;

use App\Data\DepartmentData;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $categories = Department::where(function (Builder $query) use ($request) {
            $query->where('name', 'like', '%'.$request->search.'%');
        })
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->responseSuccess([
            'results' => DepartmentData::collect($categories, DataCollection::class)->include('staffsCount'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create([
            'name' => $request->name,
            'color_code' => fake()->rgbCssColor,
        ]);

        return $this->responseSuccess([
            'result' => DepartmentData::from($department),
        ], 'Department created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreDepartmentRequest $request, Department $department)
    {
        $department->update([
            'name' => $request->name,
        ]);

        return $this->responseSuccess([
            'result' => DepartmentData::from($department->refresh()),
        ], 'Department updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $staff = $department->staffs()->first();

        if ($staff) {
            return $this->responseError('Department cannot be deleted because it has staffs', code: 400);
        }

        $department->delete();

        return $this->responseSuccess(message: 'Department deleted successfully');
    }
}
