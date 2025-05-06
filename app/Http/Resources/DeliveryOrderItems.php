<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DeliveryProductResouce;

class DeliveryOrderItems extends JsonResource
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
            'order_id' => (int)$this->order_id??0,
            'product_id' => (string)$this->product_id??"",
            'add_id' => (string)$this->add_id??"",
            'quantity' => (int)$this->quantity??0,
            'price' => (float)$this->price??0,
            'total_price' => (float)$this->total_price??0,
            // 'total_price' => (float)$this->total_price??0,
            'created_at'=>(string)$this->created_at??"",
            'updated_at'=>(string)$this->updated_at??"",
            'type'=>(string)$this->type??"",
            'product'=>new DeliveryProductResouce($this->product),
            'add'=>new DeliveryProductResouce($this->add),
            // 'add'=>$this->add,
        ];
    }
}
