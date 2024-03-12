<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

trait Zippable
{
    public function getZippableFileName(string $directory, string $fileName = 'sample'): string
    {
        $zip = new ZipArchive;
        $zipFileName = "{$fileName}.zip";

        if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === true) {
            $filesToZip = Storage::allFiles($directory);

            foreach ($filesToZip as $file) {
                $zip->addFile(str_replace('public/', 'storage/', $file), basename($file));
            }

            $zip->close();

            return public_path($zipFileName);
        } else {
            throw new Exception('Failed to create the zip file.');
        }
    }
}
