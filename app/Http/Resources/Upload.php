<?php

namespace App\Http\Resources;

use App\Traits\WhenMorphed;
use Illuminate\Http\Resources\Json\JsonResource;

class Upload extends JsonResource
{
    use WhenMorphed;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [

            'url' => $this->url,
            $this->mergeWhen($request->user()->isAdmin(), function () {
                return [
                    'id' => $this->id,
                    'key' => $this->key,
                    'path' => $this->path,
                    'drive' => $this->drive,
                ];
            }),
            'uploadable' =>  $this->WhenMorphed('uploadable')
        ];
    }
}
