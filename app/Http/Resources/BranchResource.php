<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id??0,
            'services_id' => $this->services_id??0,
            'name' => $this->name??"",
            'image' => $this->image??"",
            'address' => $this->address??"",
            'phone' => $this->phone??"",
            'start_work_date' => $this->start_work_date??"",
            'end_work_date' => $this->end_work_date??"",
            'created_at' => $this->created_at??"",
            'updated_at' => $this->updated_at??"",
        ];
    }
}
