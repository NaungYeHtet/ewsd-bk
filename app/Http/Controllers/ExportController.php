<?php

namespace App\Http\Controllers;

use App\Exports\DataCsvExport;
use App\Exports\DataXlsxExport;
use App\Http\Requests\ExportRequest;

class ExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ExportRequest $request)
    {
        return match($request->type){
            'xlsx' => (new DataXlsxExport())->download('all_idea.xlsx'),
            'csv' => (new DataCsvExport())->download('all_idea.csv'),
        };
        // return Excel::download(new AllDataExport(), 'all_data.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
