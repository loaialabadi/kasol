<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name??"",
            'email' => $this->email??"",
            'address' => $this->address??"",
            'phone' => $this->phone??"",
            'image' => $this->image??"",
            'age' =>  (int) $this->age??0,
            'gender' => $this->gender??"",
            'is_verified' => $this->is_verified??false,
            'created_at' => $this->created_at??"",
            'updated_at' => $this->updated_at??"",
        ];
    }
}
