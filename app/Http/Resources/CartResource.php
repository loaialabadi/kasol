<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'cart_id' => $this->id,
            'type' => $this->product_id ? 'product' : 'add',
            'quantity' => $this->quantity,
            'price' => number_format($this->price, 2, '.', ''),
            'total_price' => number_format($this->total_price, 2, '.', ''),
            'product' => $this->when($this->product_id && $this->relationLoaded('product'), function () {
                return new ProductResource($this->product);
            }),
            'add' => $this->when($this->add_id && $this->relationLoaded('add'), function () {
                return new AddResource($this->add);
            }),
        ];
    }
}
