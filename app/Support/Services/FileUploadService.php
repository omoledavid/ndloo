<?php

declare(strict_types=1);

namespace App\Support\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public static function uploadFile(
        UploadedFile $file,
        ?string $folder = 'gifts',
        ?string $disk = 'public'
    ) {

        return $file->store(
            $folder,
            $disk
        );
    }

    public function deleteFile(string $path, ?string $disk = 'public')
    {
        Storage::disk($disk)->delete($path);
    }
}
