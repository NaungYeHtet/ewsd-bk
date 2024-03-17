<?php

namespace App\Http\Controllers;

use App\Data\ReportData;
use App\Http\Requests\IndexRequest;
use App\Models\Report;
use App\Models\Staff;
use Spatie\LaravelData\PaginatedDataCollection;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $request->validate([
            'staff' => ['exists:staffs:uuid'],
        ]);

        $reports = Report::when($request->has('search'), function ($query) use ($request) {
            $query->where('reason', 'like', '%'.$request->search.'%');
        })->when($request->has('staff'), function ($query) use ($request) {
            $staff = Staff::where('uuid', $request->staff)->first();
            $query->where('staff_id', $staff->id);
        })->orderBy('created_at', 'desc')->paginate($request->perpage ?? 10);

        return $this->responseSuccess([
            'results' => ReportData::collect($reports, PaginatedDataCollection::class)->include('staff', 'target'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }
}
