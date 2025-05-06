<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServiceResource;
use App\Models\Favorite;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class FavoriteController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
   public function index(Request $request) {
    try {
        
          if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Token Nedded'],203);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        // return $accessToken->tokenable_id;
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        // $favorites = Favorite::with(['service', 'product'])
        //     ->where('user_id', Auth::id())
        //     ->get();
        // return Auth::id();
            $product_ids=Favorite::with(['service', 'product'])
            ->where('user_id', $accessToken->tokenable_id)
            ->where('product_id','!=',null)
            ->pluck('product_id');
            $products=Product::whereIn('id',$product_ids)->get();
            $services_ids=Favorite::with(['service', 'product'])
            ->where('user_id', $accessToken->tokenable_id)
            ->where('service_id','!=',null)
            ->pluck('service_id');
            $services=Service::whereIn('id',$services_ids)->get();
        // $products = $favorites->filter(function ($favorite) {
        //     return $favorite->product;
        // })->map(function ($favorite) {
        //     return $favorite->product;
        // });

        // $services = $favorites->filter(function ($favorite) {
        //     return $favorite->service;
        // })->map(function ($favorite) {
        //     return $favorite->service;
        // });
        // return $services;

        return response()->json([
            'success' => true,
            'message' => 'Favorites fetched successfully',
            'data' => [
                'products' => ProductResource::collection($products),
                'services' => ServiceResource::collection($services),
            ],
        ]);
    } catch (\Exception $e) {
        // return $e;
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching favorites',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'nullable|exists:services,id',
                'product_id' => 'nullable|exists:products,id',
            ]);
            
            
          if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Token Nedded'],203);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
            
            $existingFavorite = Favorite::where('user_id', $accessToken->tokenable_id);
                // ->where('service_id', $request->service_id)
                // ->where('product_id', $request->product_id)
                // ->first();
            if($request->service_id){
                // return 'f';
                $existingFavorite->where('service_id', $request->service_id);
            }
            else {
                $existingFavorite->where('product_id', $request->product_id);

            }
            $existingFavorite=$existingFavorite->first();
            // return $existingFavorite;
            if ($existingFavorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'services (or product) is already in favorites'
                ], 400);
            }
            $favorite = Favorite::create([
                'user_id' => $accessToken->tokenable_id,
                'service_id' => $request->service_id,
                'product_id' => $request->product_id,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Added to favorites',
                'favorite' => $favorite
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding to favorites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param  int  $service_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($favoriteId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }
            $favorite = Favorite::where('user_id', $user->id)->findOrFail($favoriteId);
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => 'Favorite removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the favorite',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeProducts($productId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $favorite = Favorite::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->first();

            if (!$favorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in favorites',
                ], 404);
            }
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product removed from favorites',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the product from favorites',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeservices($servicesId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $favorite = Favorite::where('user_id', $user->id)
                ->where('service_id', $servicesId)
                ->first();

            if (!$favorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'services not found in favorites',
                ], 404);
            }

            $favorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'services removed from favorites',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the services from favorites',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
