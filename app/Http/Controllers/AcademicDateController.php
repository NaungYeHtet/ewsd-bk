<?php

namespace App\Http\Controllers;

use App\Data\AcademicDateData;
use App\Http\Requests\IndexRequest;
use App\Models\AcademicDate;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\PaginatedDataCollection;

class AcademicDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dates = AcademicDate::when($request->has('search'), function (Builder $query) use ($request) {
            $query->where('academic_year', 'like', '%'.$request->search.'%');
        })->orderBy('final_closure_date', 'desc')
            ->paginate($request->perpage ?? 5);

        return $this->responseSuccess([
            'results' => AcademicDateData::collect($dates, PaginatedDataCollection::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicDateData $data)
    {
        if (AcademicDate::where('start_date', '<=', $data->startDate)->where('final_closure_date', '>=', $data->startDate)->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'start_date' => ['The start date cannot overlap an existing academic year'],
            ]);
        }

        if (AcademicDate::where('start_date', '<=', $data->finalClosureDate)->where('final_closure_date', '>=', $data->finalClosureDate)->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'final_closure_date' => ['The final closure date cannot overlap an existing academic year'],
            ]);
        }

        $academicDate = AcademicDate::create([
            'academic_year' => $data->academicYear,
            'start_date' => $data->startDate,
            'closure_date' => $data->closureDate,
            'final_closure_date' => $data->finalClosureDate,
        ]);

        return $this->responseSuccess([
            'academic_date' => AcademicDateData::from($academicDate),
        ], 'Academic date created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicDate $date)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicDateData $data, AcademicDate $date)
    {
        if (AcademicDate::where('start_date', '<=', $data->startDate)->where('final_closure_date', '>=', $data->startDate)->where('uuid', '!=', $date->uuid)->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'start_date' => ['The start date cannot overlap an existing academic year'],
            ]);
        }

        if (AcademicDate::where('start_date', '<=', $data->finalClosureDate)->where('final_closure_date', '>=', $data->finalClosureDate)->where('uuid', '!=', $date->uuid)->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'final_closure_date' => ['The final closure date cannot overlap an existing academic year'],
            ]);
        }

        $date->update([
            'academic_year' => $data->academicYear,
            'start_date' => $data->startDate,
            'closure_date' => $data->closureDate,
            'final_closure_date' => $data->finalClosureDate,
        ]);

        return $this->responseSuccess([
            'academic_date' => AcademicDateData::from($date),
        ], 'Academic date updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicDate $date)
    {
        $date->delete();

        return $this->responseSuccess([], 'Academic date deleted successfully');
    }
}
