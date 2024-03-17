<?php

namespace App\Traits;

use Exception;
use ZipArchive;

trait Zippable
{
    public function getZippableFileName(array $filesToZip, string $fileName = 'sample'): string
    {
        $zip = new ZipArchive;
        $zipFileName = "{$fileName}.zip";

        if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === true) {

            foreach ($filesToZip as $file) {
                $zip->addFile('storage/'.$file, basename($file));
            }

            $zip->close();

            return public_path($zipFileName);
        } else {
            throw new Exception('Failed to create the zip file.');
        }
    }
}
