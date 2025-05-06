<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resource\DeliveryOrderItems;
use App\Http\Resources\DeliveryOrderItems;
class DeliveryProductResouce extends JsonResource
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
            'id' =>(int) $this->id??0,
            'service_id' => (int)$this->service_id??0,
            'name' => (string)$this->name??"",
            'image' => (string)$this->image??"",
            'weight' => (string)$this->weight??"",
            'price' => (string)$this->price??"",
            
        ];
    }
}
