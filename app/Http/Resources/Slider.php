<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Slider extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'button' => $this->button,
            'image' => $this->image,
            'active' => $this->active,

        ];
    }
}
