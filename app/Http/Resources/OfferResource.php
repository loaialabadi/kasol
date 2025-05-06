<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'id' => $this->id??0,
            'title' => $this->title??"",
            'description' => $this->description??"",
            'image' => $this->image_url??"", 
            'start_date' => $this->start_date??"",
            'end_date' => $this->end_date??"",
            'price' =>(string) $this->price??"0",
            'product' => new ProductResource($this->product),
            // 'service' => new ServiceResource($this->service),
            'descounted_price' => $this->descounted_price??"0",
            'stars_rate' => $this->stars_rate??"0",
            'service_name'=>isset($this->service)?$this->service->name:"",
            'service_logo'=>isset($this->service)?$this->service->logo:"",
            'created_by' => $this->created_by??"",
            'status' => $this->status??"",
            'created_at' => $this->created_at??"",
            'updated_at' => $this->updated_at??"",
            // 'image_url' => $this->image_url,
        ];
    }
}
