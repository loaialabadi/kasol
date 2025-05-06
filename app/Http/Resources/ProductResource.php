<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = Auth::user();
        
        $isFavorite = $user
            ? \App\Models\Favorite::where('user_id', $user->id)
                ->where('product_id', $this->id)
                ->exists()
            : false;

        return [
            'id' => $this->id,
            'name' => $this->name??"",
            'slug' => $this->slug??"",
            'description' => $this->description??"",
            'is_favorite' => $isFavorite??0, 
            'image' => $this->image??"",
            'price' => $this->price??"",
            'descounted_price' => $this->descounted_price??"",
            'status' => $this->status??"",
            'is_in_offer'=>$this->is_in_offer??false,
            'productsize'=>$this->productsize??"",
            'image_id' => $this->image_id??0,
            'sub_category' => $this->whenLoaded('subCategory'),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'user' => $this->whenLoaded('user'),
            'add' => $this->whenLoaded('add'),
            'sizes' => $this->whenLoaded('sizes'),
            'created_at' => $this->created_at??"",
            'updated_at' => $this->updated_at??"",
        ];
    }
}

