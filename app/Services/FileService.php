<?php

namespace App\Services;

use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Storage;

class FileService extends Service
{
    public function storeFile($file, string $partialPath, $decode = false)
    {
        if ($decode && isset($file['base64'])) {
            $file = FileHelper::fromBase64($file["base64"]);
        }

        $fileName = $file->getClientOriginalName();
        $path = Storage::disk('public')->putFileAs($partialPath, $file, $fileName);
        
        return $path;
    }

    function removeEverythingFromPath($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->deleteDirectory($path);
        }
    }
}
