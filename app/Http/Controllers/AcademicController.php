<?php

namespace App\Http\Controllers;

use App\Data\AcademicData;
use App\Exports\DataCsvExport;
use App\Exports\DataXlsxExport;
use App\Http\Requests\ExportRequest;
use App\Http\Requests\IndexRequest;
use App\Models\Academic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\PaginatedDataCollection;

class AcademicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dates = Academic::when($request->has('search'), function (Builder $query) use ($request) {
            $query->where('name', 'like', '%'.$request->search.'%');
        })->orderBy('final_closure_date', 'desc')
            ->paginate($request->perpage ?? 5);

        return $this->responseSuccess([
            'results' => AcademicData::collect($dates, PaginatedDataCollection::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicData $data)
    {
        if (Academic::where('start_date', '<=', $data->startDate)->where('final_closure_date', '>=', $data->startDate)->exists()) {
            throw ValidationException::withMessages([
                'start_date' => ['The start date cannot overlap an existing academic year'],
            ]);
        }

        if (Academic::where('start_date', '<=', $data->finalClosureDate)->where('final_closure_date', '>=', $data->finalClosureDate)->exists()) {
            throw ValidationException::withMessages([
                'final_closure_date' => ['The final closure date cannot overlap an existing academic year'],
            ]);
        }

        $academic = Academic::create([
            'name' => $data->name,
            'start_date' => $data->startDate,
            'closure_date' => $data->closureDate,
            'final_closure_date' => $data->finalClosureDate,
        ]);

        return $this->responseSuccess([
            'academic' => AcademicData::from($academic),
        ], 'Academic created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Academic $academic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicData $data, Academic $academic)
    {
        if (Academic::where('start_date', '<=', $data->startDate)->where('final_closure_date', '>=', $data->startDate)->where('uuid', '!=', $academic->uuid)->exists()) {
            throw ValidationException::withMessages([
                'start_date' => ['The start date cannot overlap an existing academic year'],
            ]);
        }

        if (Academic::where('start_date', '<=', $data->finalClosureDate)->where('final_closure_date', '>=', $data->finalClosureDate)->where('uuid', '!=', $academic->uuid)->exists()) {
            throw ValidationException::withMessages([
                'final_closure_date' => ['The final closure date cannot overlap an existing academic year'],
            ]);
        }

        $academic->update([
            'name' => $data->name,
            'start_date' => $data->startDate,
            'closure_date' => $data->closureDate,
            'final_closure_date' => $data->finalClosureDate,
        ]);

        return $this->responseSuccess([
            'academic' => AcademicData::from($academic),
        ], 'Academic updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Academic $academic)
    {
        if ($academic->ideas()->exists()) {
            return $this->responseError("Unable to delete academic {$academic->name} because it has ideas submitted", code: 400);
        }

        $academic->delete();

        return $this->responseSuccess([], 'Academic deleted successfully');
    }
}
