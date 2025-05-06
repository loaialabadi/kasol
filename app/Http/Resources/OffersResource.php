<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OffersResource extends JsonResource
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
            'title' => $this->title??"",
            'description' => $this->description??"",
            'image' => $this->image??"",
            'start_date' => $this->start_date??"",
            'end_date' => $this->end_date??"",
            'price' => $this->price??"",
            'product' => new ProductResource($this->product),
            // 'service' => $this->service,
            'descounted_price' => $this->descounted_price??"",
            'stars_rate' => $this->stars_rate??"",
            'created_by' => $this->created_by??"",
            'status' => $this->status??"",
            // 'created_at' => $this->created_at??"",
            // 'updated_at' => $this->updated_at??"",
        ];
    }
}
