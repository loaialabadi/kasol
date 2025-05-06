<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total_price' => $this->total_price,
            'payment_method' => $this->payment_method,
            'shipping_address' => $this->shipping_address,
            'branch_id' => $this->branch_id,
            'delivery_cost'=>$this->delivery_cost,
            'status' => $this->status,
            'notes' => $this->notes,
            'receiving_method' => $this->receiving_method,
            'online_method' => $this->online_method,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'deliver_phone' => isset($this->delivery)?$this->delivery->phone:"",
            // 'cart' => $this->cart_id,
            'delivery_phone'=>isset($this->delivery)?$this->delivery->phone:"",
            'order_items'=>$this->order_items,
            // 'cartItems' => $this->cart && $this->cart->items  // Accessing the cart items
                // ? CartItemResource::collection($this->cart->items)  // Mapping through CartItems
                // : [],        
                ];
    }
}
