<?php

declare(strict_types=1);

namespace App\Support\Helpers;

use App\Contracts\Interfaces\FileUploadInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUpload implements FileUploadInterface
{
    public static function uploadFile(
        UploadedFile $file,
        ?string $folder = 'avatars',
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
