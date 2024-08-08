<?php

namespace App\Traits;

trait WhenMorphed
{
    public function whenMorphed($relationship)
    {
        return $this->whenLoaded($relationship, function () use ($relationship) {
            $morphType = $relationship . '_type';
            $morphAlias = $this->resource->$morphType;
            $morphResourceClass = "App\\Http\\Resources\\" . class_basename($morphAlias);
            return new $morphResourceClass($this->resource->{$relationship});
        });
    }
}
