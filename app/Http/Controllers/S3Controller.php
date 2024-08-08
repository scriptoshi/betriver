<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Aws\Sdk;
use Http;
use Illuminate\Http\Request;
use Storage;
use Str;

class S3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function sign(Request $request, $disk="do", $folder= "logos")
    {
        $spaces = Storage::disk($disk);
        $client = $spaces->getClient();
        $expiry = "+10 minutes";
        $random =  Str::random(20);
        $fileName = $folder.'/'. $random.'.'. $request->ext;
        $cmd = $client->getCommand('PutObject', [
            'Bucket' => \Config::get("filesystems.disks.{$disk}.bucket"),
            'Key' =>    $fileName,
            'ACL' => 'public-read',
        ]);
        $signed = $client->createPresignedRequest($cmd, $expiry);
        $presignedUrl = (string) $signed->getUri();
        $deletCommand = $client->getCommand('DeleteObject', [
            'Bucket' => \Config::get("filesystems.disks.{$disk}.bucket"),
            'Key' =>   $fileName,
            'contentType'=>$request->type
        ]);
        $delete = $client->createPresignedRequest($deletCommand, $expiry);
        $deleteUrl = (string)$delete->getUri();
        $cdn = config("filesystems.disks.{$disk}.cdn");
        $url = str($cdn)->endsWith('/')? $cdn . $fileName: $cdn .'/'. $fileName;
        return response()->json(['url' => $presignedUrl, 'file' => $fileName, 'link'=> $url, 'remove'=> $deleteUrl ], 201);
    }

    public function purge($fileName)
    {
        $folder = config('filesystems.do.folder');
        Http::asJson()->delete(
            config('filesystems.do.cdn_endpoint') . '/cache',
            [
                'files' => ["{$folder}/{$fileName}"],
            ]
        );
    }


}

