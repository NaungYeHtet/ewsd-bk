<?php

namespace App\Data;

use App\Exports\DataCsvExport;
use App\Exports\DataXlsxExport;
use App\Models\Academic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use ZipArchive;

/** @typescript */
class AcademicData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string|Optional $id,
        public string $name,
        #[MapInputName('start_date'), WithCast(DateTimeInterfaceCast::class)]
        public Carbon $startDate,
        #[MapInputName('closure_date'), WithCast(DateTimeInterfaceCast::class)]
        public Carbon $closureDate,
        #[MapInputName('final_closure_date'), WithCast(DateTimeInterfaceCast::class)]
        public Carbon $finalClosureDate,
        public bool|Optional $isActive,
        public string|Optional|null $dataDownloadCsvUrl,
        public string|Optional|null $dataDownloadXlsxUrl,
        public string|Optional|null $fileExportUrl,
    ) {
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'start_date' => ['required', 'date', 'after:today'],
            'closure_date' => ['required', 'date', 'after:start_date'],
            'final_closure_date' => ['required', 'date', 'after:closure_date'],
        ];
    }

    public static function fromModel(Academic $academic): self
    {
        $dataFilePath = "academic/exports/{$academic->data_file_name}";
        $filesDataPath = "academic/exports/{$academic->files_file_name}";

        if (! Storage::exists("{$dataFilePath}.csv")) {
            Excel::store(new DataCsvExport($academic), "{$dataFilePath}.csv");
        }

        if (! Storage::exists("{$dataFilePath}.xlsx")) {
            Excel::store(new DataXlsxExport($academic), "{$dataFilePath}.xlsx");
        }

        if (! Storage::exists("{$filesDataPath}.zip")) {
            // Storage::disk('FTP')->put('new/file1.jpg', Storage::get('old/file1.jpg'));

            // $zip = new ZipArchive;
            // $zipFileName = 'sample.zip';

            // if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === true) {

            //     $filesToZip = Storage::files("/academic/files/{$academic->start_date->format('Y-m-d')}-{$academic->final_closure_date->format('Y-m-d')}");

            //     foreach ($filesToZip as $file) {
            //         $zip->addFile($file, basename($file));
            //     }

            //     $zip->close();

            //     Storage::put("{$filesDataPath}.zip", $zipFileName);
            // } else {
            //     return 'Failed to create the zip file.';
            // }
        }

        if (config('filesystems.default') == 's3') {
            $csvUrl = Storage::temporaryUrl("{$dataFilePath}.csv", now()->addMinutes(30));
            $xlsxUrl = Storage::temporaryUrl("{$dataFilePath}.xlsx", now()->addMinutes(30));
            $zipUrl = Storage::temporaryUrl("{$filesDataPath}.zip", now()->addMinutes(30));
        } else {
            $csvUrl = url('/').Storage::url("{$dataFilePath}.csv");
            $xlsxUrl = url('/').Storage::url("{$dataFilePath}.xlsx");
            $zipUrl = url('/').Storage::url("{$filesDataPath}.zip");
        }

        return new self(
            $academic->uuid,
            $academic->name,
            $academic->start_date,
            $academic->closure_date,
            $academic->final_closure_date,
            now()->between($academic->start_date, $academic->final_closure_date),
            $academic->is_previous_academic ? $csvUrl : null,
            $academic->is_previous_academic ? $xlsxUrl : null,
            $academic->is_previous_academic ? $zipUrl : null,
        );
    }
}
