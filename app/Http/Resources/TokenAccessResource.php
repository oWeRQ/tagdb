<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenAccessResource extends JsonResource
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
            'preset_id' => $this->id,
            'can_create' => $this->can_create,
            'can_read' => $this->can_read,
            'can_update' => $this->can_update,
            'can_delete' => $this->can_delete,
        ];
    }
}
