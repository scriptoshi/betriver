<?php

namespace App\Actions;

use App\Models\Setting;
use File;
use Illuminate\Http\Request;
use Storage;


class SettingsUploads
{
    /**
     * save uploads to settings table
     */

    public static function filepond(object $file)
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

    public function upload(Request $request, Setting $setting, $key = null)
    {
        $uri_key = $key ? "{$key}_uri" : 'uri';
        $upload_key = $key ? "{$key}_upload" : 'upload';
        $path_key = $key ? "{$key}_path" : 'path';
        // user provided a file
        if ($request->input($upload_key) == false)
            return static::url($request, $setting, $key);
        // user uploaded a file
        if (settings('uploads_disk') === 'public') {
            $path = static::filepond((object)$request->input($path_key));
            $url = Storage::disk('public')->url($path);
        } else {
            $url = $request->input($uri_key);
            $path = $request->input($path_key);
        }
        $setting->val = $url;
        $setting->save();
        //delete old file
        if ($upload = $setting->logo)  $upload->removeFile();
        $setting->logo()->updateOrCreate(
            [
                'key' => $setting->name
            ],
            [
                'url' => $url,
                'path' => $path,
                'drive' => settings('uploads_disk')
            ]
        );
    }

    public function validation($uploadkey = null): array
    {
        $key = $uploadkey ? "{$uploadkey}_" : "";
        return [
            "{$key}uri" => ['required', 'string'],
            "{$key}upload" => ['required', 'boolean'],
            "{$key}path" => ['nullable', 'string', "required_if:{$key}upload,true"],
        ];
    }

    /**
     * user provided a file url;
     */
    private static function url(Request $request, Setting $setting, $key = null)
    {
        $uri_key = $key ? "{$key}_uri" : 'uri';
        $url = $request->input($uri_key);
        $setting->val = $url;
        $setting->save();
    }
}
