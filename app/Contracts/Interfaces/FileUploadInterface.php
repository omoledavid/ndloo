<?php

namespace App\Contracts\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileUploadInterface
{
    public static function uploadFile(UploadedFile $file, ?string $folder = 'avatars', ?string $disk = 'public');

    public function deleteFile(string $path, ?string $disk = 'public');
}
