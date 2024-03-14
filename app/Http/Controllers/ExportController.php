<?php

namespace App\Http\Controllers;

use App\Exports\DataCsvExport;
use App\Exports\DataXlsxExport;
use App\Http\Requests\ExportRequest;
use App\Models\AcademicDate;
use App\Models\Idea;
use App\Traits\Zippable;

class ExportController extends Controller
{
    use Zippable;

    /**
     * Handle the incoming request.
     */
    public function data(ExportRequest $request, AcademicDate $date)
    {
        return match ($request->type) {
            'xlsx' => (new DataXlsxExport($date))->download('all_idea.xlsx'),
            'csv' => (new DataCsvExport($date))->download('all_idea.csv'),
        };
    }

    public function files(AcademicDate $date)
    {
        $fileNames = Idea::where('created_at', '>=', $date->start_date)
            ->where('created_at', '<=', $date->closure_date)
            ->whereNotNull('file')
            ->pluck('file')->toArray();

        if (! count($fileNames)) {
            return $this->responseError("No files within academic {$date->academic_year}", code: 200);
        }

        return response()->download($this->getZippableFileName($fileNames, 'idea-uploads'))->deleteFileAfterSend(true);
    }
}
