<?php

namespace App\Actions;

use File;
use Illuminate\Http\Request;
use Storage;
use Illuminate\Database\Eloquent\Model;

class Uploads
{
    // This class manages the sites uploads model.

    private static function filepond(object $file)
    {
        $filepond = app(\Sopamo\LaravelFilepond\Filepond::class);
        $path = $filepond->getPathFromServerId($file->serverId);
        $disk = config('filepond.temporary_files_disk');
        $fullpath = Storage::disk($disk)->path($path);
        $extension = $file->fileExtension ?? File::guessExtension($fullpath);
        $uploadedFile = 'uploads/' . File::hash($fullpath) . '.' . $extension;
        File::move($fullpath, Storage::disk('public')->path($uploadedFile));
        return $uploadedFile;
    }

    /**
     * Handle uploads for profile photos
     */
    public function uploadProfilePhoto(Request $request,  $key = null)
    {
        $upload_key = $key ? "{$key}_upload" : 'upload';
        $path_key = $key ? "{$key}_path" : 'path';
        // user provided a file
        if ($request->input($upload_key) == false) return false;
        $request->validate([$path_key => "required"]);
        // local disk
        if (settings('profile_photo_disk', 'public') === 'public') {
            return static::filepond((object)$request->input($path_key));
        }
        return $request->input($path_key);
    }

    public function upload(Request $request, Model $uploadable, $key = null)
    {
        $uri_key = $key ? "{$key}_uri" : 'uri';
        $upload_key = $key ? "{$key}_upload" : 'upload';
        $path_key = $key ? "{$key}_path" : 'path';

        // user provided a file
        if ($request->input($upload_key) == false) return static::url($request, $uploadable, $key);

        // user uploaded a file
        if (settings('uploads_disk') === 'public') {
            $path = static::filepond((object)$request->input($path_key));
            $url = Storage::disk('public')->url($path);
        } else {
            $url = $request->input($uri_key);
            $path = $request->input($path_key);
        }
        //delete uploaded file if any
        if ($upload =  $uploadable->uploads()->where('key', $key)->first())
            $upload->removeFile();

        return  $uploadable->uploads()->updateOrCreate([
            'key' => $key
        ], [
            'url' => $url,
            'path' => $path,
            'drive' => settings('uploads_disk')
        ]);
    }

    public function validation($uploadkey = null): array
    {
        $key = $uploadkey ? "{$uploadkey}_" : "";
        return [
            "{$key}uri" => ['required', 'string'],
            "{$key}upload" => ['required', 'boolean'],
            "{$key}path" => ['nullable',  "required_if:{$key}upload,true"],
        ];
    }

    /**
     * user provided a file url;
     */
    private static function url(Request $request, Model $uploadable, $key = null)
    {
        $uri_key = $key ? "{$key}_uri" : 'uri';
        $path_key = $key ? "{$key}_path" : 'path';
        $url = $request->input($uri_key);
        $hasPath = $request->filled($path_key);
        return $uploadable->uploads()->updateOrCreate([
            'key' => $key
        ], [
            'url' => $url,
            'is_external' =>  !$hasPath
        ]);
    }
}
