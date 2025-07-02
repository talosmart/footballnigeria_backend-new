<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (! function_exists('fileAttributes')) {
    function fileAttributes($file)
    {
        $fileName = str_ireplace(' ', '', $file->getClientOriginalName());
        $filePath = Str::uuid().'.'.\File::extension($file->getClientOriginalName());

        return [
            'fileName' => $fileName,
            'filePath' => $filePath,
        ];
    }
}

if (! function_exists('localUploadFile')) {
    function localUploadFile($file)
    {
        $path = public_path('uploads');

        if (! \File::isDirectory($path)) {
            \File::makeDirectory($path, 0777, true, true);
        }

        if (! $file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $fileAttributes = fileAttributes($file);

        $fileName = $fileAttributes['fileName'];
        $filePath = $fileAttributes['filePath'];

        $file->move(env('UPLOAD_FILE_PATH', 'uploads/'), $filePath);

        $lastMod = \File::lastModified(env('UPLOAD_FILE_PATH', 'uploads/').$filePath);
        $size = \File::size(env('UPLOAD_FILE_PATH', 'uploads/').$filePath);
        $mimeType = \File::mimeType(env('UPLOAD_FILE_PATH', 'uploads/').$filePath);

        return compact('fileName', 'filePath', 'lastMod', 'size', 'mimeType');
    }
}

if (! function_exists('s3uploadFile')) {
    function s3uploadFile($file)
    {
        if (! $file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
        
        $fileAttributes = fileAttributes($file);

        $fileName = $fileAttributes['fileName'];
        $filePath = $fileAttributes['filePath'];

        Storage::disk('s3')->put($filePath, file_get_contents($file));

        $lastMod = Storage::disk('s3')->lastModified($filePath);
        $size = Storage::disk('s3')->size($filePath);
        $mimeType = Storage::disk('s3')->mimeType($filePath);

        return compact('fileName', 'filePath', 'lastMod', 'size', 'mimeType');
    }
}

if (! function_exists('localFileRemover')) {
    function localFileRemover($filePath)
    {
        $path = public_path('uploads');

        if(\File::exists(env('UPLOAD_FILE_PATH', 'uploads/').$filePath)){
            \File::delete(env('UPLOAD_FILE_PATH', 'uploads/').$filePath);
        }
    }
}

if (! function_exists('s3FileRemover')) {
    function s3FileRemover($filePath)
    {
        Storage::disk('s3')->delete($filePath);

        return;
    }
}

if (! function_exists('s3FileDownloader')) {
    function s3FileDownloader($filePath)
    {
        $path = Storage::disk('s3')->get($filePath);
        return response()->download($path);
    }
}