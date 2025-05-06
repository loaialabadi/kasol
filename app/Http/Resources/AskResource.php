<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ask' => $this->title,
            'answer' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // 'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}