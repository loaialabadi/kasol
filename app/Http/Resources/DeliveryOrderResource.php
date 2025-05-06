<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resource\DeliveryOrderItems;
use App\Http\Resources\DeliveryOrderItems;
class DeliveryOrderResource extends JsonResource
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
            'user_id' => (int)$this->id??0,
            'delivery_cost' => (string)$this->delivery_cost??"",
            'total_price' => (string)$this->total_price??"",
            'notes' => (string)$this->notes??"",
            'user_long' => (string)$this->user_long??"",
            'user_lat' => (string)$this->user_lat??"",
                        'delivery_phone'=>isset($this->delivery)?$this->delivery->phone:"",
                        'user_phone'=>isset($this->user)?$this->user->phone:"",

            'receiving_method' => (string)$this->receiving_method??"",
            'service_id' => (string)$this->service_id??"",
            'payment_method' => (string)$this->payment_method??"",
            'online_method' => (string)$this->online_method??"",
            'shipping_address' => (string)$this->shipping_address??"",
            'status' => (string)$this->status??"",
            'created_at' => (string)$this->created_at??"",
            'updated_at' => (string)$this->updated_at??"",
            'order_notes' => (string)$this->order_notes??"",
            'deleted_at' => (string)$this->deleted_at??"",
            'size'=>$this->size,
            'branch_id' => (int)$this->branch_id??0,
            'pay_order_id' => (string)$this->pay_order_id??"",
            'payment_status' => (string)$this->payment_status??"",
            'delivery_id' => (int)$this->delivery_id??0,
            'order_items'=>DeliveryOrderItems::collection($this->order_items),
            // 'branch'=>$this->branch,
            'branch_lat'=>isset($this->branch)?(string)$this->branch->lat??"":"",
            'branch_lang'=>isset($this->branch)?(string)$this->branch->long??"":"",
            'branch_address'=>isset($this->branch)?(string)$this->branch->address??"":"",
            'service_id'=>$this->service_id??"",
            'store_name'=>isset($this->service)?$this->service->name:"",
            "store_logo"=>isset($this->service)?$this->service->logo:"",
        ];
    }
}
