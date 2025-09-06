<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;


trait FileHelperTrait
{
    use PathHelperTrait;
    /**
     * Combines filePath with backend public path (.../app/public).
     * 
     * @param string $filePath
     * @return string
     */
    public function storeProfile(Request $request, $identifier, $dynamic_path = 'user-profile', $columnName = 'profile')
    {
        // 1. If storage not exist create it.
        $folderName = "profiles/" . $dynamic_path . '/' . $identifier . '/';
        $path = $this->transformToPrivate($folderName);
        // Checks directory exist if not will be created.
        !is_dir($path) && mkdir($path, 0777, true);

        // 2. Store image in filesystem
        $fileName = null;
        if ($request->hasFile($columnName)) {
            $file = $request->file($columnName);
            if ($file != null) {
                $fileName = Str::uuid() . '.' . $file->extension();
                $file->move($path, $fileName);

                return $folderName  . $fileName;
            }
        }
        return null;
    }
    public function deleteDocument($filePath)
    {
        if (is_file($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    public function storePublicDocument(Request $request, $path, $docName = 'document')
    {
        $newPath = $this->transformToPublic($path);

        !is_dir($newPath) && mkdir($newPath, 0777, true);

        // 2. Store image in filesystem
        $fileName = null;
        if ($request->hasFile($docName)) {
            $file = $request->file($docName);
            $fileExtention = $file->extension();

            if ($file != null) {
                $fileName = Str::uuid() . '.' . $fileExtention;
                $file->move($newPath, $fileName);

                return [
                    'path' => $path . $fileName,
                    'name' => $file->getClientOriginalName(),
                    'extension' => $fileExtention,
                ];
            }
        }
        return null;
    }
}
