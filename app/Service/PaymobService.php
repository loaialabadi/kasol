<?php

namespace App\Service;

// use App\Http\Controllers\Api\BaseController;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Http;
use App\Models\{Branch, Cart, Order,User};
use App\Http\Resources\Order\{OrderCollectionResource,OrderResource};
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Modules\Associations\Entities\Association;
use Modules\UsersPayments\Entities\UsersPayment;
use stdClass;
use App\Models\CouponUser;
use App\Models\Coupon;
use App\Events\StoreOrderPlaces;
use App\Events\OrderPlaced;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log; 

class PaymobService extends BaseController
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;
    protected $cardId;
    protected $api_key;
    protected $walletId;

    /**
     * Create a new service class.
     */
    public function __construct()
    {
        $this->baseUrl = env('PAYMOB_API_URL');
        $this->secretKey = env('PAYMOB_SECRET_KEY');
        $this->publicKey = env('PAYMOB_PUBLIC_KEY');
        $this->cardId = env('PAYMOB_CARD');
        $this->walletId = env('PAYMOB_WALLET');
        $this->api_key = env('PAYMOB_API_KEY');
        // $this->walletId = env('PAYMOB_WALLET_INTEGRATION_ID');
    }



  public function createPaymentIntention($data, $order_id = null)
{
    $payment_method = 'card';
// return $this->secretKey;
// return $this->baseUrl
    // 1. Authenticate and get token
    $authResponse = Http::post($this->baseUrl . '/auth/tokens', [
        'api_key' => "ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2ljSEp2Wm1sc1pWOXdheUk2TVRBeU1UY3dNeXdpYm1GdFpTSTZJbWx1YVhScFlXd2lmUS5tQW8wTHpnUjNaRHdKVkY2TUZnaDE1LTZ1OURiWTNBLVg4WUFSZ3VOQnBvSUZZVHhWWnZsWllzOEFaeC1udDNkakwtbGJqVHVuT2lsWXVheUlEckdLUQ==",
    ]);

// return $authResponse;
    if (!$authResponse->successful()) {
        return $this->sendError('Authentication failed');
    }

    $authToken = $authResponse->json()['token'];
// return $authToken;
// \Log::info($authResponse->json());
if(!request()->header('Authorization')){
    return response()->json(['status'=>false,'message'=>'unauth'],401);
}
$token = explode(' ', request()->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
    // return $accessToken;
        // return $user;
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }

    // $user=Auth::user();
            // if($user==null){
            //     return response()->json([
            //             'success' => false,
            //             'message' => 'Session Ended Login Again',
            //         ],401);
            // }
            
            $user=User::where('id',$accessToken->tokenable_id)->first();
// return $user;
    $user_carts=Cart::with('product')->where('user_id',$user->id)->get();
$total_paid=0;
    $items=[];
            foreach($user_carts as $cart){
                // return $cart;
                $item=new stdClass();
                $item->name=$cart->product->name;
                $item->description=$cart->product->description??"no details";
                $item->amount_cents=(float)$cart->price*(int)$cart->quantity;//(float)$cart->total_price
                $item->amount_cents*=100;
                $item->quantity=$cart->quantity;
                $total_paid+=(float)$cart->total_price;
                array_push($items,$item);
            }


$total_paid=Cart::where('user_id',$user->id)->sum('total_price');
// return $total_paid;
$total_paid+=(float)request()->input('delivery_cost');
    $orderResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $authToken,
        'Content-Type' => 'application/json',
    ])->post($this->baseUrl . '/ecommerce/orders', [
        'auth_token' => $authToken,
        'delivery_needed' => false,
        'amount_cents' => $total_paid * 100,
        'currency' => 'EGP',
        'items' => $items,
    ]);
// return $orderResponse;
    if (!$orderResponse->successful()) {
        return $this->sendError('Order creation failed');
    }

    $orderId = $orderResponse->json()['id'];

    // 3. Generate Payment Key
    $billing = [
        "apartment" => "123",
        "first_name" => $user->name,
        "last_name" => "noooo name",
        "street" => 'no address',
        "building" => "456",
        "phone_number" => $user->phone,
        "city" => 'no city',
        "country" => "EG",
        "email" => $user->email,
        "floor" => "1",
        "state" => 'no state',
        "postal_code" => "12345",
        "shipping_method" => "PKG"
    ];
    // $total_paid+=(float)request()->input('delivery_cost');
// return request()->input('online_pay_type');
    $paymentKeyResponse = Http::withHeaders([
        'Authorization' => 'Bearer ' . $authToken,
        'Content-Type' => 'application/json',
    ])->post($this->baseUrl . '/acceptance/payment_keys', [
        'auth_token' => $authToken,
        'amount_cents' => $total_paid * 100,
        'expiration' => 3600,
        'order_id' => $orderId,
        'billing_data' => $billing,
        'currency' => 'EGP',
        'integration_id' => request()->input('online_pay_type')=='card'?4944620:4924031,
    ]);

    if (!$paymentKeyResponse->successful()) {
        return $this->sendError('Payment key generation failed');
    }

    $paymentKey = $paymentKeyResponse->json()['token'];

    $branch=Branch::where('id',request('branch_id'))->first();
$discount_price=0;
            
            if(request()->input('coupon_id')!=0){
                 $code = Coupon::where('id', request()->input('coupon_id'))->first();
                 if ($code->type == 'flat') {
        $discount_value = (int)$code->value;
    } elseif ($code->type == 'percentage') {
        $discount_value = ($total_paid * $code->value) / 100;
    }
    $discount_price = max($total_paid - $discount_value, 0); // Ensure it doesn't go below 0
    $code->increment('much_used');
  CouponUser::create([
        'user_id' => $accessToken->tokenable_id,
        'coupon_id' => $code->id,
    ]);
    }

    $new_order=Order::create([
        'user_id'=>$user->id,
        // 'delivery_cost'=>20,
        'total_price'=>(float)$total_paid-$discount_price,
        'discount_price'=>$discount_price,
        'notes'=>request()->input('notes'),
        'receiving_method'=>request()->input('receiving_method'),
        'user_lat'=>request()->input('user_lat'),
        'order_notes'=>request()->input('order_notes'),
        'user_long'=>request()->input('user_long'),
        'delivery_cost'=>request()->input('delivery_cost'),
        'service_id'=>$branch->service_id,
        'payment_method'=>request('payment_method'),
        // 'online_method'=>'',
        'shipping_address'=>request('shipping_address'),
        'branch_id'=>request('branch_id'),
    ]);
foreach($user_carts as $cart){
    // return $cart;
    $item_data=[];
    $item_data['order_id']=$new_order->id;
    $item_data['product_id']=$cart->product_id;
    $item_data['add_id']=$cart->add_id;
    $item_data['quantity']=$cart->quantity;
    $item_data['price']=$cart->price;
    $item_data['size_id']=$cart->size_id;
    $item_data['discount_price']=$cart->discount_price;
    
    $item_data['total_price']=(float)$cart->price*(int)$cart->quantity;
    $item_data['type']=$cart->type;
    $order_item=OrderItems::create($item_data);
}
Cart::where('user_id',$user->id)->delete();
$new_order->update(['pay_order_id'=>$orderId,'payment_method'=>'online']);

//   event(new OrderPlaced($new_order,$branch->branch_id));
//             event(new StoreOrderPlaces($new_order,$branch->service_id));


    return [
        'payment_id' => $orderId,
        'redirect_url' => "https://accept.paymob.com/api/acceptance/iframes/898236?payment_token=" . $paymentKey
    ];
}




}