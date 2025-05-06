<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        $productPrice = $this->product->price ?? 0;
        $addPrice = $this->add->price ?? 0;
        $totalPrice = ($productPrice + $addPrice) * $this->quantity;
        $totalPriceFormatted = number_format($totalPrice, 2, ',', '.');

        return [
            'cart_id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')), 
            'add' => new AddResource($this->whenLoaded('add')),
            'quantity' => $this->quantity,
            'size'=>$this->size,
            'total_price' => $totalPriceFormatted, 
        ];
    }
}