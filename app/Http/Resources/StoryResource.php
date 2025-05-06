<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id??0,
            'user' => new UserResource($this->whenLoaded('user')), 
            'file' => $this->file??"",
            'file_path' => asset('storage/' . $this->file_path)??"",
            'created_at' => $this->created_at??"",
            'updated_at' => $this->updated_at??"",
        ];
    }
}
