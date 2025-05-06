<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\DeliveryOrderResource;
use App\Models\Cart;
use App\Models\Service;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItems;
use App\Models\Branch;
use App\Models\DeliveryWallets;
use App\Models\DeliveryOrders;
use App\Events\OrderPlaced;
use App\Models\Delivery;
use App\Models\ServiceOrder;
use App\Events\StoreOrderPlaces;
use App\Events\DeliveryOrderPlaced;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

use stdClass;
class OrderController extends Controller
{
     public $project_id = "kassol-app";
    public $private_key_id = "3099f6fbce4513ab7577fb2959a867bef7c80b67";
    public $private_key = "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCykGLiCj30FXyZ\nxgyvRw8kPXvnuUH3xY0j9ROnx76mEaLgYZTVCV8WjXO32f58/aDGsOTV0PmS8hHT\nGfOtJY5FVNRgADYTk3jm4zzIeLT3evOkiBFfJkejtdhFfU1aEqd7zVViwBA9hhLd\n93uepF01MzNElyNKEjB/oU044ofsmpFg+2CaVbnQrFsvOGm3pCTJzwAS9M93mp2l\nUfUX79zaR6rMYmtSLhCH/OTFxbR+DgGKBsyAswBoJ9eWK5Nm/NV7RwhgJQeW8TrI\ny+DSobNrsecdLdBfl7Q0yRTBBMexLfGpHZzrWOfhNF9APGQ40XBxdiF6ntHGH2Jh\nei4K7FJxAgMBAAECggEAJhiwhB3fyGjiJ9OthqLQO/DjM9XeWWy8N1j1C9WNmxRF\nrf4buRTyBNeCgg3gIg4hpGp+tBCNArkjLOkLSFxsHWYjFmVNJEGRKJ+AKxw3rdo9\nS5PdMvHxlF+6Z977JxW2Rm9RQYuNG3c0pMdFiFv0Z+KH1GEzffzh/ITvOwz3p3aZ\nxBtSO8ZRwz4xmm/SoXx0xyPXw4XpubZZ7fPSvjD1GwGTXXq4C/p5Zr1oBxk6JV4I\nnMe1VwY9yXbfEYWPMTNnTF9BHzeM+L4p8f95xyoHyq41Ya+JwYtjQrTpaan1MciA\nsSdxp1gYLvvX4VLlGkWyA16rW/itg7bNjXLOL5LRfQKBgQDzGx6GzXMfoqIZYAY6\nCDzEEzeCUA9Kr8Eac4DMBrLvcM3+pZOSSg2VMqUSxQnM3poiqP5G8yK+/R6bXdCW\n2p/82LBR+l06kcK8njSiYPo/pSts5LRKNeeymELuzj8jisNxdlRkb/oVoGdC4+Of\n+b0zk2M1fwX2ZkwkHs8lhRNqNQKBgQC8COt6W9TGFWD1DSZ1SkEqsz0wVdc83GQQ\nDHwuN2m7qam+spFR9WaqqsyFZx4QSQbQWYg+GR8u3OH2RKzLyW9XxGsHCi6s3nSs\nDgMS6XPzNf6fwX7gPJa/hN/4OsPeKqj6/lVBYkmF3xYCoQlr70+Nr7w5j0o6wIgc\nukO9s3fuzQKBgBf7R+KiZ8XqJDV+nH6jC2OIRcSMI0YJ2nHCjSUTUBmAtkF1kf4v\nYfGi4lfcqh/Uob3jj9nt6DKRA7Dt60LGZFvsAFDF8uZxNJWowaZ3DJi0sXj3grWD\nZczfalLmc6IM8UTDGIYj6baycR1PH8hLOZfZ5UKEulDZPI+fnm2kGAs1AoGBAJwo\noD9G4ob7HEGHAUhpOYOXxZpNv+n4clVtuPe851+FwMXxkIRLVE8qd53WjH4j17fS\nkzZXMLF/mV0nnYXErWZrZiM7jpXcTXLSVDUxki0Ky4KrdI4PY3Z8iHCjhSUNsrZ3\n4B/j24GFSsc2LBfqb9wQdTYsTQ5L9P4+pqF12YWFAoGBANCJBlW/QjUu7tQhpS86\nKgcBD/dNhT5xutw9MJyXc7qYKr+LqYB4LjDl/MjJC7XUz1ef4J6Gj7Q3j0vBjVnM\njnqVxcjO01rjFXBVrQXQxl7AmRsCtp9HNBVSxPY0Gb6onC4xikSyljMgk/50Sac7\nqWPVaHK8xngnO5LCAxX9Hu0s\n-----END PRIVATE KEY-----\n";
    public $client_email = "firebase-adminsdk-c585f@kassol-app.iam.gserviceaccount.com";
    public $client_id = "104880607320193342622";
    public $auth_uri = "https://accounts.google.com/o/oauth2/auth";
    public $token_uri = "https://oauth2.googleapis.com/token";
    public $auth_provider_x509_cert_url = "https://www.googleapis.com/oauth2/v1/certs";
    public $client_x509_cert_url = "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-c585f%40kassol-app.iam.gserviceaccount.com";
    public $universe_domain = "googleapis.com";
    
    
    
        public function admin_orders(){
        $deliveries=Delivery::where('ban',"active")->get();
        $orders=Order::with('user','service','branch','delivery')->where('show_ord',1)->orderBy('created_at','desc')->paginate(10);
        // return $orders;
        return view('orders.admin_orders',compact('orders','deliveries'));
    }
        public function changeOrderStatus(Request $request)
{
    $order = Order::find($request->id);

    if (!$order) {
        return response()->json(['success' => false, 'message' => 'Order not found'], 404);
    }

    $order->status = $request->status;
    $order->save();

    return response()->json(['success' => true, 'message' => 'Order status updated successfully']);
}


  public function order_details(){
      $id=request('id');
      $order=Order::with('order_items.product','order_items.size','user','service')->where('id',$id)->first();
      return view('orders.order_details',compact('order'));
  }
  
  public function delete_orders(){
      $id=request('id');
      $del=Order::where('id',$id)->delete();
      $del_ord_serv=ServiceOrder::where('order_id',$id)->delete();
      $del_ord_del=DeliveryWallets::where('order_id',$id)->delete();
      if($del){
          return redirect()->back()->with('success','Success To Delete');
      }
                return redirect()->back()->with('error','Faild To Delete');

  }
  
public function search_orders(Request $request)
{
    $searchQuery = $request->get('search');
    // $id=request('id');
    // Fetch subcategories based on the search query
//  return $id;
$deliveries=Delivery::where('ban',"active")->get();
$orders = Order::with('user','service','branch','delivery')->whereHas('user', function ($query) use ($searchQuery) {
    $query->where('name', 'LIKE', '%' . $searchQuery . '%');
})
->orWhereHas('service',function($query) use ($searchQuery){
    $query->where('name', 'LIKE', '%' . $searchQuery . '%');

})
->paginate(10);
    // Return the results to the view
    return view('orders.admin_orders', compact('orders','deliveries'));
}

    // public function index()
    // {
    //     try {
    //         $orders = Order::where('user_id', Auth::id())
    //             ->whereNotNull('cart_id')
    //             ->with(['cart.items'])
    //             // ->whereHas('cart.items', function ($query) {
    //             //     $query->where('cart_id', '=', DB::raw('cart_id'));
    //             // })
    //             ->orderBy('cart_id')
    //             ->get();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Orders fetched successfully.',
    //             'data' => OrderResource::collection($orders),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while fetching orders.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function index()
    {
        // return  Auth::id();
        try {
            $orders = Order::with('order_items.product','order_items.add','delivery')->where('show_ord',1)->where('user_id', Auth::id())
                // ->whereNotNull('cart_id')
                // ->with(['cart'])
                // ->orderBy('cart_id')
                ->orderBy('id','desc')
                ->get();
            // ->whereHas('cart.items', function ($query) {
            //     $query->where('cart_id', '=', DB::raw('cart_id'));
            // })

            $orders = $orders->map(function ($order) {
                if ($order->cart && $order->cart->isEmpty()) {
                    $order->cartItems = 'No items available';
                } else {
                    $order->cartItems = $order->cart;
                }
                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Orders fetched successfully.',
                'data' => OrderResource::collection($orders),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($order)
    {
        try {
            $order = Order::with(['cart.items'])
                ->whereHas('cart', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('user_id', Auth::id())
                ->findOrFail($order);

            return response()->json([
                'success' => true,
                'message' => 'Order fetched successfully.',
                'data' => new OrderResource($order),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(Request $request)
    {
        
           $token = explode(' ', $request->header('Authorization'))[1];

    $accessToken = PersonalAccessToken::findToken($token);

    if($accessToken==null){
            return response()->json([
                    'success' => false,
                    'message' => 'Session Ended Login Again',
                ],401);
    }
        // return 'eer';
        try {
            $validated = $request->validate([
                // 'cart_id' => 'required|exists:carts,id',
                // 'total_price' => 'required|numeric',
                'shipping_address' => 'nullable',
                'payment_method' => 'required|in:cash,card,online',
                'online_method' => 'nullable|string|required_if:payment_method,card',
                'notes' => 'nullable|string',
                'receiving_method' => 'required|in:pickup,delivery',
                'service_id' => 'nullable|exists:services,id',
                'branch_id' => 'required|exists:branches,id',
                'user_lat'=>'nullable',
                'order_notes'=>'nullable',
                'user_long'=>'nullable',
                'delivery_cost'=>'nullable'
            ]);
            $user=Auth::user();
            if($user==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
            }
            $user_carts=Cart::where('user_id',$user->id)->get();
            // return $user_carts[0]->branch_id;
            // return $request->branch_id;
            // $branches=Branch::get();
            // return $branches;
            $branch=Branch::where('id',$request->branch_id)->first();
            // return $branch;
            $total_price=Cart::where('user_id',$user->id)->sum('total_price');
            $discount_price=0;
            $discount_value=0;
            if($request->coupon_id!=0){
                 $code = Coupon::where('id', $request->coupon_id)->first();
                 if ($code->type == 'flat') {
        $discount_value = (int)$code->value;
    } else if ($code->type == 'percentage') {
        $discount_value = ($total_price * $code->value) / 100;
    }
    $discount_price = max($total_price - $discount_value, 0); // Ensure it doesn't go below 0
    $code->increment('much_used');
  CouponUser::create([
        'user_id' => $accessToken->tokenable_id,
        'coupon_id' => $code->id,
    ]);


            }
            $order_total_price=(float)$total_price+(float)$request->delivery_cost;
            // return $user_carts[0]->service_id;
            $new_order=Order::create([
                    'user_id'=>$accessToken->tokenable_id,
                    'delivery_cost'=>$request->delivery_cost,
                    'user_long'=>$request->user_long,
                    'user_lat'=>$request->user_lat,
                    'order_notes'=>$request->order_notes,
                    'total_price'=>$order_total_price-$discount_value,
                    'notes'=>$request->notes,
                    'discount_price'=>$discount_value,
                    'receiving_method'=>$request->receiving_method,
                    'service_id'=>$branch->service_id,
                    'payment_method'=>$request->payment_method, 
                    // 'online_method'=>'',
                    'show_ord'=>1,
                    'shipping_address'=>$request->shipping_address,
                    'branch_id'=>$request->branch_id,
                ]);
             $service=Service::where('id',$new_order->service_id)->first();
                $total_value=(float)$new_order->total_price-(float)$new_order->delivery_cost;
                $companyRatio = ($service->service_ratio / 100) * $total_value;
                $serv_order=ServiceOrder::create([
                        'service_id'=>$new_order->service_id,
                        'order_id'=>$new_order->id,
                        'order_value'=>$total_value,
                        'discount_price'=>$new_order->discount_price,
                        'pay_method'=>'cash',
                        'date'=>now(),
                        'company_ratio'=>$companyRatio
                    ]);
                    $service_money=$total_value-$companyRatio;
                $up=$service->update(['money'=>(float)$service->money-$companyRatio]);
                // return $up;
            foreach($user_carts as $cart){
                // return $cart;
                $item_data=[];
                $item_data['order_id']=$new_order->id;
                $item_data['product_id']=$cart->product_id;
                $item_data['add_id']=$cart->add_id;
                $item_data['size_id']=$cart->size_id??null;
                $item_data['quantity']=$cart->quantity;
                $item_data['price']=$cart->price;
                $item_data['total_price']=(float)$cart->price*(int)$cart->quantity;
                $item_data['type']=$cart->type;
                $item_data['discount_price']=$cart->discount_price;
                $order_item=OrderItems::create($item_data);
            }
            // Cart::where('user_id',$user->id)->delete(); 
              event(new OrderPlaced($new_order,$request->branch_id));
            event(new StoreOrderPlaces($new_order,$branch->service_id));


    Cart::where('user_id',$user->id)->delete();
            // $user_carts->delete();
            return response()->json(['success' => true,
                'message' => 'Order created successfully.'],200);
            // $new_order->update(['branch']);
            // return $user_carts;
            $cart = Cart::find($request->cart_id);

            if (!$cart || $cart->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found or unauthorized.',
                ], 404);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $request->total_price,
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'status' => 'pending',
                'show_ord'=>1,
                'cart_id' => $cart->id,
            ]);

            $cartItems = $cart->products()->get();
            foreach ($cartItems as $product) {
                $order->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                ]);
            }

            $cart->delete();
            // return 'rerere';
                    event(new OrderPlaced($order));

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            // return $e;
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $order)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($order);

            $request->validate([
                'total_price' => 'required|numeric',
                'shipping_address' => 'required|string',
                'payment_method' => 'required|in:cash,card,online',
                'online_method' => 'nullable|string|required_if:payment_method,card',
                'notes' => 'nullable|string',
                'receiving_method' => 'nullable|in:pickup,delivery',
                'service_id' => 'nullable|exists:services,id',
                'status' => 'required|string|in:pending,processing,completed,cancelled',
            ]);

            DB::beginTransaction();
            $order->update([
                'total_price' => $request->total_price,
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'status' => $request->status,
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully.',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($order)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($order);
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function resturant_orders(){
        $user=Auth::id();
        return $user;
    }
    public function user_orders(){
        // return 'eer';
        $user=Auth::user();
        if($user==null){
            return response()->json([
                 'success' => false,
                'message' => 'Session Ended Login Again',
                ],401);
        }
        $orders=Order::with('branch')->where('user_id',$user->id)->where('show_ord',1)->get(['id','delivery_cost','total_price','notes','receiving_method','payment_method','branch_id','shipping_address','status']);
        return response()->json([
               'success' => true,
                'message' => 'Success',
                'data'=>$orders
            ],200);
        // return $orders; 
    }
    public function order_items(){
        $id=request('id');
        $items=OrderItems::with('product','add','size')->where('order_id',$id)->get();
         return response()->json([
               'success' => true,
                'message' => 'Success',
                'data'=>$items
            ],200); 
    }
    public function branch_orders(){
        // return 'ere';
        $id=request('id');
        $orders=Order::where('branch_id',$id)->where('show_ord',1)->get(['id','delivery_cost','total_price','notes','receiving_method','payment_method','shipping_address','status','order_notes']);
        return response()->json([
               'success' => true,
                'message' => 'Success',
                'data'=>$orders
            ],200);
    }
    
     public function store_orders(){
        // return 'ere';
        $status=request()->query('status');
        $id=request('id');
        $user=Auth::user();
        // return $user;
        if($user==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $brans_ids=null;
        if(!isset($user->service_id)){
            $brans_ids=Branch::where('service_id',$user->id)->pluck('id')->toArray();
        }
        else {
            $brans_ids=[]; 
            array_push($brans_ids,$user->id);
        }
        
        // return $brans_ids;
          $orders = Order::where('show_ord',1)->whereIn('branch_id', $brans_ids);

if ($status == 'delivered') {
    $orders->where('status', 'delivered');
} elseif ($status == 'current') {
    $orders->whereIn('status', ['accepted', 'processing', 'shipped']);
} else {
    $orders->where('status', 'pending');
}

// Fetch the results
$orders = $orders->get(['id', 'delivery_cost', 'total_price', 'notes', 'receiving_method', 'payment_method', 'shipping_address', 'status','created_at','order_notes']);

return response()->json([
    'success' => true,
    'message' => 'Success',
    'data' => $orders
], 200);
    }
    public function callBack(Request $request){
        return $request;
    }
    
    
    public function assign_delivery(Request $request){
    $delivery_id=$request->delivery_id;
    $order_id=$request->order_id;
    // return $delivery_id;
    
    
    // $del_asign=DeliveryOrders::where('order_id',$order_id)->delete();
    // $assign=DeliveryOrders::create([
    //     'delivery_id'=>$delivery_id,
    //     'order_id'=>$order_id,
    // ]);
    // $assign=Order::where('id',$order_id)->update(['delivery_id'=>$delivery_id]);
    
    
    //   $assign=DeliveryOrders::create([
    //             'delivery_id'=>$del_id,
    //             'order_id'=>$order_id
    //         ]);
            $order=Order::with('order_items.product','order_items.size','order_items.add','branch')->where('id',$order_id)->first();
            $order->update(['delivery_id'=>$delivery_id]);
            $orderResource = new DeliveryOrderResource($order); // Store resource separately
event(new DeliveryOrderPlaced($orderResource, $delivery_id));
    
    
    if($order){
        
        $check_assign_bef=DeliveryWallets::where('order_id',$order->id)->first();
        if($check_assign_bef){
            $delivery=Delivery::where('id',$check_assign_bef->delivery_id)->first();
            $delivery->update(['money'=>(float)$delivery->money-$order->delivery_cost]);
            $check_assign_bef->update(['delivery_id'=>$delivery_id]);
            $new_del=Delivery::where('id',$delivery_id)->first();
            $new_del->update(['money'=>(float)$new_del->money+(float)$order->delivery_cost]);
        }
        else {
            $del_order=DeliveryWallets::create([
                        'delivery_id'=>$delivery_id,
                        'order_id'=>$order->id,
                        'delivery_cost'=>$order->delivery_cost,
                        'pay_method'=>$order->payment_method,
                        'service_id'=>$order->service_id,
                        'branch_id'=>$order->branch_id,
                        'user_long'=>$order->user_long,
                        'user_lat'=>$order->user_lat,
                        'total'=>$order->total_price,
                        'date'=>now()
                        ]);
            $delivery=Delivery::where('id',$delivery_id)->first();
            // return $delivery;
            $delivery->update(['money'=>(float)$delivery->money+(float)$order->delivery_cost]);
        }
        
        // return $order->branch_id;
        return response()->json(['status'=>'success','message'=>'Success To Assign'],200);
        // return redirect()->back()->with('success','Success To Assign');
    }
    return response()->json(['status'=>'error','message'=>'Faild To Assign'],200);

        // return redirect()->back()->with('error','Faild To Assign');
}
    
      public function service_change_status(Request $request){
    $token = explode(' ', $request->header('Authorization'))[1];

    $accessToken = PersonalAccessToken::findToken($token);

    if($accessToken==null){
            return response()->json([
                    'success' => false,
                    'message' => 'Session Ended Login Again',
                ],401);
    }
    // $id=request('id');
    $id=$request->id; 
    $status=$request->status;
    // $new_status=request()->query('status');
    $change_status=Order::where('id',$id)->update(['status'=>$status]);
    $order=Order::where('id',$id)->first();
    if($change_status){
        if($status=='rejected'){
            $check_exist=ServiceOrder::where('order_id',$order->id)->first();
            $money_get=(float)$check_exist->order_value-(float)$check_exist->company_ratio;
            $serv=Service::where('id',$check_exist->service_id)->first();
            $update_serv_money=$serv->update(['money'=>(float)$serv->money-$money_get]);
            if($check_exist){
                $check_exist->delete();
            }
        }
        else {
            $check_exist=ServiceOrder::where('order_id',$order->id)->first();
            if($check_exist==null){
                $service=Service::where('id',$new_order->service_id)->first();
                $total_value=(float)$order->total_price-(float)$order->delivery_cost;
                $companyRatio = ($service->service_ratio / 100) * $total_value;
                $serv_order=ServiceOrder::create([
                    'service_id'=>$order->service_id,
                    'order_id'=>$order->id,
                    'order_value'=>$total_value,
                    'pay_method'=>$order->payment_method,
                    'company_ratio'=>$companyRatio
                ]);
                
                $service_money=$total_value-$companyRatio;
                $up=$service->update(['money'=>(float)$service->money-$companyRatio]);
            }
        }
        
         $user=User::where('id',$order->user_id)->first();
            
            // return $user;
            
          $tokenUrl = "https://oauth2.googleapis.com/token";
        
        $privateKey=$this->private_key;
                $jwt = $this->generateJwt($privateKey, $this->client_email);

    
        $tokenRequestData = array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        );
        
        $accessTokenTwo = null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenRequestData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);

        if ($response === false) {
            // return response()->json(['error' => curl_error($ch)], curl_errno($ch));
        }
        
         $responseData = json_decode($response, true);

        if (isset($responseData['access_token'])) {
            $accessToken = $responseData['access_token'];
            $accessTokenTwo = $accessToken;
        } else {
            return response()->json(['error' => 'Failed to retrieve access token.']);
        }

        curl_close($ch);
        

        // Send FCM Notification
        $msg = array(
            'title' => 'حاله الطلب',
            'body' =>"تم تغيير حاله الطلب رقم $order->id إلى $status",
        );

        $topic = 'child'.$request->child_id;
        // return '/topics/' . $topic
// return $user->fir_token;

        $fields = array(
            'message' => array(
                'token' => $user->fcm_token,
                'notification' => $msg,
            ),
            
        );

        $serverKey = $accessTokenTwo;
// return $serverKey;
        $headers = array(
            'Authorization: Bearer ' . $serverKey,
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/kassol-app/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
            // return $result;
        
        
        
        
        
        
        return response()->json(['status'=>true,'message'=>'success to Update Status'],200);
    }
    return response()->json(['status'=>false,'message'=>'Faild to Update Status'],203);

}
    public function assign_order(){
        $del_id=3;
        $order_id=275;
        $assign=DeliveryOrders::create([
                'delivery_id'=>$del_id,
                'order_id'=>$order_id,
                
            ]);
            $order=Order::with('order_items.product','order_items.size','order_items.add','branch')->where('id',$order_id)->first();
            $order->update(['delivery_id'=>$del_id]);
            $orderResource = new DeliveryOrderResource($order); // Store resource separately
event(new DeliveryOrderPlaced($orderResource, $del_id)); // Pass raw model instead

             return 'success';
        
    }
    
    
     public function generateJwt($privateKey, $clientId)
    {
        $now = time();
        $exp = $now + 3600; // Token valid for 1 hour

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];
        $base64UrlHeader = $this->base64url_encode(json_encode($header));

        $payload = [
            'iss' => $clientId,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $exp,
            'iat' => $now
        ];
        $base64UrlPayload = $this->base64url_encode(json_encode($payload));

        $signature = '';
        openssl_sign("$base64UrlHeader.$base64UrlPayload", $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $base64UrlSignature = $this->base64url_encode($signature);

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    
    
   public function store_history_orders()
{
    $status = request()->query('status');
    $id = request('id');

    // Get date range from request
    $from_date = request()->query('from_date');
    $to_date = request()->query('to_date');

    // Logic for handling date defaults
    if (!$from_date && !$to_date) {
        // No dates provided -> Use today
        $from_date = $to_date = Carbon::today()->toDateString();
    } elseif ($from_date && !$to_date) {
        // Only from_date provided -> Use from_date to today
        $to_date = Carbon::today()->toDateString();
    }

    $user = Auth::user();
    // return $user;
    if ($user == null) {
        return response()->json([
            'success' => false,
            'message' => 'Session Ended Login Again',
        ], 401);
    }

    $brans_ids = null;
    if (!isset($user->service_id)) {
        $brans_ids = Branch::where('service_id', $user->id)->pluck('id')->toArray();
    } else {
        $brans_ids = [];
        array_push($brans_ids, $user->id);
    }
// return $brans_ids;
// return $user;
    // Filter orders by branch and date range
    $orders = Order::where('show_ord', 1)
        ->where('service_id', $user->id)
        ->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']); // Full day range

    // Fetch results
    $orders = $orders->get([
        'id',
        'delivery_cost',
        'total_price',
        'notes',
        'receiving_method',
        'payment_method',
        'shipping_address',
        'status',
        'created_at'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Success',
        'data' => $orders
    ], 200);
}

    public function delivery_wallets(Request $request){
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
    
        if($accessToken==null){
            return response()->json([
                    'success' => false,
                    'message' => 'Session Ended Login Again',
                ],401);
        }   
        
         $from_date = request()->query('from_date');
        $to_date = request()->query('to_date');
    
        // Logic for handling date defaults
        if (!$from_date && !$to_date) {
            // No dates provided -> Use today
            $from_date = $to_date = Carbon::today()->toDateString();
        } elseif ($from_date && !$to_date) {
            // Only from_date provided -> Use from_date to today
            $to_date = Carbon::today()->toDateString();
        }
        
        
        
        $delivery_id=$accessToken->tokenable_id;
        // return $delivery_id;
        $order_numbers=DeliveryWallets::where('delivery_id',$delivery_id)->whereBetween('date', [$from_date , $to_date])->count();
        $cash_orders=DeliveryWallets::where('delivery_id',$delivery_id)->where('pay_method','cash')->whereBetween('date', [$from_date , $to_date])->count();
        $online_orders=DeliveryWallets::where('delivery_id',$delivery_id)->where('pay_method','online')->whereBetween('date', [$from_date , $to_date])->count();
        $money_cash=DeliveryWallets::where('delivery_id',$delivery_id)->where('pay_method','cash')->whereBetween('date', [$from_date , $to_date])->sum('delivery_cost');
        $money_online=DeliveryWallets::where('delivery_id',$delivery_id)->where('pay_method','online')->whereBetween('date', [$from_date , $to_date])->sum('delivery_cost');
        $delivery=Delivery::where('id',$delivery_id)->first();
        $total_money=$delivery->money;
        
        $data=new stdClass();
        
        $data->order_numbers=$order_numbers;
        $data->cash_orders=$cash_orders;
        $data->online_orders=$online_orders;
        $data->money_cash=$money_cash;
        $data->money_online=$money_online;
        $data->total_money=$total_money;
        return response()->json([
                'status'=>true,
                'data'=>$data,
                'message'=>''
            ],200);
        // $wallets=DeliveryWallets::with('order','service','branch')->where('delivery_id',$delivery_id)->get();
    }
    
    
    
    
       public function delivery_history_orders(Request $request)
{
    
    
     $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
    
        if($accessToken==null){
            return response()->json([
                    'success' => false,
                    'message' => 'Session Ended Login Again',
                ],401);
        }   
        $delivery_id=$accessToken->tokenable_id;
    // return $delivery_id;
    $status = request()->query('status');
    $id = request('id');

    // Get date range from request
    $from_date = request()->query('from_date');
    $to_date = request()->query('to_date');

    // Logic for handling date defaults
    if (!$from_date && !$to_date) {
        // No dates provided -> Use today
        $from_date = $to_date = Carbon::today()->toDateString();
    } elseif ($from_date && !$to_date) {
        // Only from_date provided -> Use from_date to today
        $to_date = Carbon::today()->toDateString();
    }

    

//   return $delivery_id; 
    // Filter orders by branch and date range
    $orders = Order::where('show_ord', 1)
        ->where('delivery_id', $delivery_id)
        ->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']); // Full day range

    // Fetch results
    $orders = $orders->get([
        'id',
        'delivery_cost',
        'total_price',
        'notes',
        'receiving_method',
        'payment_method',
        'shipping_address',
        'status',
        'created_at'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Success',
        'data' => $orders
    ], 200);
}
    
}
