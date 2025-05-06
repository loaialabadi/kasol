<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => asset('storage/' . $this->image), 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

