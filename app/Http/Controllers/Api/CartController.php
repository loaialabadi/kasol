<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Add;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use stdClass;
use App\Models\Offer;
use App\Models\User;
use App\Models\ProductSize;
use App\Models\Setting;
use App\Models\Service;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Http;
class CartController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    
   public function store(Request $request)
{
    try {
        $discount_price=0;
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'إنتهت الجلسه سجل من جديد',
            ], 401);
        }

        // Validate input
        $validated = $request->validate([
            'id'       => 'required',
            'type'     => 'required|in:product,add',
            'quantity' => 'required|integer|min:1',
            'size_id'  => 'nullable|integer',
            'is_offer' => 'nullable|boolean',
        ]);

        $type = $request->type;
        $id = $request->id;
        $sizeId = $request->size_id;

        // Fetch item based on type
        $item = $type === 'add' ? Add::find($id) : Product::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'هذا العنصر غير موجود',
            ], 422);
        }
        $service=Service::where('id',$item->service_id)->first();
        if($service){
            if($service->open_status==0){
                 return response()->json([
                'success' => false,
                'message' => 'مغلق الان',
            ], 422);
            }
        }
        // Handle product size
        $sizeItem = null;
        if ($type === 'product' && $sizeId) {
            $sizeItem = ProductSize::where(['product_id' => $id, 'size_id' => $sizeId])->first();
            if (!$sizeItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجم المحدد غير متوفر',
                ], 422);
            }
        }

        // Handle offers
        $finalPrice = $sizeItem ? (float)$sizeItem->price : (float)$item->price;
        if ($request->is_offer) {
            $offer = Offer::where('product_id', $id)->first();
            if ($offer) {
                $discountedPrice = $finalPrice - ($finalPrice * ((float)$offer->descounted_price / 100));
                $finalPrice = $discountedPrice;
                $discount_price=$finalPrice * ((float)$offer->descounted_price / 100);
            }
        }

        // Ensure user orders from one service only
        $userCartServices = Cart::where('user_id', $user->id)->pluck('service_id')->toArray();
        if (count($userCartServices) > 0 && !in_array($item->service_id, $userCartServices)) {
            return response()->json([
                'status' => false,
                'message' => 'يجب عليك الطلب من محل واحد',
            ], 422);
        }

        // Prepare cart data
        $cartData = [
            'user_id'     => $user->id,
            'quantity'    => (int)$request->quantity,
            'total_price' => $finalPrice * (int)$request->quantity,
            'price'       => $finalPrice,
            'type'        => $type,
            'service_id'  => $item->service_id,
            'discount_price'=>$discount_price,
        ];

        if ($type === 'product') {
            $cartData['product_id'] = $id;
            $cartData['size_id'] = $sizeId ?? null;
        } else {
            $cartData['add_id'] = $id;
        }

        // Check if item already exists in cart
        $existingCartItem = Cart::where([
            'user_id' => $user->id,
            'product_id' => $type === 'product' ? $id : null,
            'add_id' => $type === 'add' ? $id : null,
            'size_id' => $sizeId ?? null
        ])->first();

        if ($existingCartItem) {
            $updated = $existingCartItem->update([
                'quantity'    => $request->quantity,
                'total_price' => $finalPrice * (int)$request->quantity,
            ]);
            return response()->json([
                'status' => (bool)$updated,
                'message' => $updated ? 'تم تحديث الكمية بنجاح' : 'فشل تحديث الكمية',
            ], $updated ? 200 : 410);
        }

        // Add new item to cart
        Cart::create($cartData);

        return response()->json([
            'success' => true,
            'message' => 'تمت الإضافة بنجاح',
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ غير متوقع أثناء إضافة المنتج إلى السلة',
            // 'error'   => $e->getMessage(),
        ], 500);
    }
}


    protected function getItemPrice(string $type, int $id): float
    {
        if ($type === 'product') {
            return Product::findOrFail($id)->price;
        } elseif ($type === 'add') {
            return Add::findOrFail($id)->price;
        }
        throw new \InvalidArgumentException("Invalid item type: $type");
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // return 'reer';
        $user=Auth::user();
        if($user==null){
              return response()->json([
                'success' => false,
                'message' => 'Session Ended Login Again',
                'data' => [
                ],
            ], 401);
        }
        
        $products=Cart::with('product.subCategory','size')->where('user_id',$user->id)->where('type','product')->get();
        $adds=Cart::with('add')->where('user_id',$user->id)->where('type','add')->get();
        
        $total_price=Cart::where('user_id',$user->id)->sum('total_price');
        
        $data=new stdClass();
        
        $new_products = $products->map(function ($cartItem) {
    $cartItem->name = $cartItem->product->name; 
    $cartItem->description = $cartItem->product->description; 
    $cartItem->description = $cartItem->product->description; 
    $cartItem->subCategory = $cartItem->product->subCategory; 
    $cartItem->slug = $cartItem->product->slug;
    $cartItem->image = $cartItem->product->image; 
    // unset($cartItem->product);
    unset($cartItem->items);
    return $cartItem;
});
        
        $new_adds = $adds->map(function ($cartItem) {
    $cartItem->name = $cartItem->add->name; 
    $cartItem->image = $cartItem->add->image; 
    $cartItem->weight = $cartItem->add->weight; 
 
    // $cartItem->slug = $cartItem->product->slug;
    $cartItem->image = $cartItem->add->image; 
    unset($cartItem->add);
    unset($cartItem->items);
    return $cartItem;
});
        
        $data->products=$new_products;
        $data->adds=$adds;
        $data->total_price=$total_price;
        
        $branches=null;
            // return $adds;
            $service=null;
        if(count($products)>0){
            $service=Service::where('id',$products[0]->service_id)->first();
            $branches=Branch::where('service_id',$products[0]->service_id)->get(['id','name','image']);
            
        }
        if(count($adds)>0) {
              $branches=Branch::where('service_id',$adds[0]->service_id)->get(['id','name','image']);
        }
        
        $data->branches=$branches??[];
        $data->has_delivery=isset($service)?(int)$service->has_delivery:0;
        return response()->json([
                'status'=>true,
                'message'=>'Success To Get Data',
                'data'=>$data
            ],200);
        // return 
        // $user_cart=Cart::where('user_id',$user->id)->get();
        // return $user_cart;
        
        $cart = Cart::with(['items.product', 'items.add'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'message' => 'Cart is empty',
                'data' => [],
            ], 200);
        }

        $products = $cart->items->filter(function ($item) {
            return $item->product_id;
        });

        $adds = $cart->items->filter(function ($item) {
            return $item->add_id;
        });

        return response()->json([
            'success' => true,
            'message' => 'Cart items fetched successfully',
            'data' => [
                'products' => CartItemResource::collection($products),
                'adds' => CartItemResource::collection($adds),
                'total_price' => number_format($cart->items->sum('total_price'), 2, '.', ''),
            ],
        ], 200);
    }

    /**
     * @param  int  $cartItemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($cartItemId)
    {
        try {
            $cart = Auth::user()->cart;
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }//
            $cartItem = Cart::where('id', $cartItemId)->findOrFail($cartItemId);
            $cartItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing from cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeProducts(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'exists:cart_items,product_id',
            ]);
            $cart = Auth::user()->cart;
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found',
                ], 404);
            }
            CartItem::where('cart_id', $cart->id)
                ->whereIn('product_id', $validated['product_ids'])
                ->delete();
            return response()->json([
                'success' => true,
                'message' => 'Products removed from cart',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing products from cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeAdds(Request $request)
    {
        try {
            $validated = $request->validate([
                'add_ids' => 'required|array|min:1',
                'add_ids.*' => 'exists:cart_items,add_id',
            ]);
            $cart = Auth::user()->cart;
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found',
                ], 404);
            }
            CartItem::where('cart_id', $cart->id)
                ->whereIn('add_id', $validated['add_ids'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Adds removed from cart',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing adds from cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function service_orders(){
        $user=Auth::user();
        return $user;
        // $orders=Order::where('')
    }
    
    
   public function calc_delivery_cost(Request $request)
{
    $branch_id = $request->id;
    $branch = Branch::where('id', $branch_id)->first();

    if (!$branch) {
        return response()->json(['status' => false, 'message' => 'Branch not found'], 404);
    }

    if (!$request->header('Authorization')) {
        return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
    }

    $token = explode(' ', $request->header('Authorization'))[1] ?? null;
    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken) {
        return response()->json([
            'success' => false,
            'message' => 'Session Ended. Login Again',
            'data' => [],
        ], 401);
    }

    $url = "https://maps.googleapis.com/maps/api/distancematrix/json";

    $response = Http::get($url, [
        'origins' => "{$request->lat},{$request->long}",
        'destinations' => "{$branch->lat},{$branch->long}",
        'key' => 'AIzaSyDBTIqoGXzQosZjNvLW2pm5iK8fQcF5fHk' // Store API key in .env
    ]);

    $setting=Setting::first();
    $data = $response->json();
    // $setting=Setting::first();

    if ($data["status"] == "OK" && isset($data['rows'][0]['elements'][0]['distance'])) {
        $kilo_arr = explode(" ", $data['rows'][0]['elements'][0]['distance']['text']);
        $distance_km = (float) $kilo_arr[0];

        // If the distance is 3 km or less, the cost is 15
        if ($distance_km <= 3) {
            $delivery_cost = $setting->start_order_price;
        } else {
            // Else, cost is 15 plus the price per kilometer for the extra distance
            $extra_km = $distance_km - 3;
            $delivery_cost = $setting->start_order_price + ($extra_km * (float) $setting->delivery_price);
        }

        return response()->json([
            'status' => true,
            'data' => round($delivery_cost)
        ]);
    }

    return response()->json(['status' => false, 'message' => 'Error in calculating delivery cost', 'data' => 0]);
}
}
