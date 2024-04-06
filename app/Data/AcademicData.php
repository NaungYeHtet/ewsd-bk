<?php

namespace App\Data;

use App\Exports\DataCsvExport;
use App\Exports\DataXlsxExport;
use App\Models\Academic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

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
        public string|Optional $dataDownloadCsvUrl,
        public string|Optional $dataDownloadXlsxUrl,
        public string|Optional $fileExportUrl,
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
            // $fileSystem = new Filesystem(new ZipArchiveAdapter(new FilesystemZipArchiveProvider('images/files')));

            // $filesToZip = $academic->ideas()
            //     ->whereNotNull('file')
            //     ->pluck('file')->toArray();

            // foreach ($filesToZip as $file) {
            //     $fileContent = Storage::get($file);
            //     $fileSystem->write($file, $fileContent);
            // }

            // self::makeZippableFile($filesToZip, "{$filesDataPath}.zip");
            // Storage::put("{$filesDataPath}.zip");
        }

        if (config('filesystems.default') == 's3') {
            $csvUrl = Storage::temporaryUrl("{$dataFilePath}.csv", now()->addMinutes(30));
            $xlsxUrl = Storage::temporaryUrl("{$dataFilePath}.xlsx", now()->addMinutes(30));
            // $zipUrl = Storage::temporaryUrl("{$dataFilePath}.zip", now()->addMinutes(30));
        } else {
            $csvUrl = url('/').Storage::url("{$dataFilePath}.csv");
            $xlsxUrl = url('/').Storage::url("{$dataFilePath}.xlsx");
        }

        return new self(
            $academic->uuid,
            $academic->name,
            $academic->start_date,
            $academic->closure_date,
            $academic->final_closure_date,
            now()->between($academic->start_date, $academic->final_closure_date),
            $csvUrl,
            $xlsxUrl,
            'tbc',
        );
    }
}
