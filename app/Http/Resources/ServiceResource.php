<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        $user = Auth::user();

        $isFavorite = $user
            ? \App\Models\Favorite::where('user_id', $user->id)
                ->where('service_id', $this->id)
                ->exists()
            : false;
        $userStory = null;

        if ($user && $user->stories()->withoutGlobalScopes()->exists()) {
            $userStory = $user->stories()->withoutGlobalScopes()->first();
        }

        $subCategories = $this->whenLoaded('products', function () {
            return $this->products->load('subCategory')
                ->pluck('subCategory')
                ->unique('id')
                ->map(function ($subCategory) {
                    return [
                        'id' => isset($subCategory)?$subCategory->id:0,
                        'name' => isset($subCategory)?$subCategory->name:"",
                    ];
                })->values();
        });

        return [
            'id' => $this->id??0,
            'is_favorite' => $isFavorite??0,
            'story' => $userStory ? [new StoryResource($userStory)] : [],
            'baner' => $this->baner??"",
            'logo' => $this->logo??"",
            'menu' => $this->menu??"",
            'rating' => $this->rating??"",
            'name' => $this->name??"",
            'address' => $this->address??"",
            'phone' => $this->phone??"",
            'email' => $this->email??"",
            'featured' => $this->featured??"",
            'status' => $this->status??"",
            'description' => $this->description??"",
            'price_range' => $this->price_range??"",
            'start_work_date' => $this->start_work_date??"",
            'category_id' => $this->category_id??0,
            'end_work_date' => $this->end_work_date??"",
            'images' => $this->whenLoaded('images', function () {
                return ImageResource::collection($this->images);
            }),
            'branches' =>isset($this->branches)? BranchResource::collection($this->branches):[],
            'products' => $this->whenLoaded('products', function () {
                return ProductResource::collection($this->products);
            }),
            'adds' => $this->whenLoaded('adds', function () {
                return AddResource::collection($this->adds);
            }),
            'sub_categories' => $subCategories,
            'offers' => $this->whenLoaded('offers', function () {
                return OfferResource::collection($this->offers);
            }),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at??"",
            'updated_at' => $this->updated_at??"",
        ];
    }
}
