<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ServProductResource extends JsonResource
{
    public function toArray($request)
    {
     

        return [
            'id' => $this->id,
            'name' => $this->name??"",
            'slug' => $this->slug??"",
            'description' => $this->description??"",
            'image' => $this->image??"",
            'price' => $this->price??0,
            'status' => $this->status??"",
            'image_id' => $this->image_id??0,
            'sub_category_id' => $this->sub_category_id??0,
            'service_id' => $this->service_id??0,
            'user_id' => $this->user_id??0,
            'is_in_offer'=>$this->is_in_offer??false,
            'add_id' => $this->add_id??0,
            'created_at' => $this->created_at??"",
            'updated_at' => $this->updated_at??"",
            'deleted_at' => $this->deleted_at??"",
            'productsize' => $this->productsize??[],
           
        ];
    }
}
