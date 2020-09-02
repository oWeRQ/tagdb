<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntityResource extends JsonResource
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
            'name' => $this->name,
            'tags' => TagResource::collection($this->tags),
            'values' => ValueResource::collection($this->values),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
