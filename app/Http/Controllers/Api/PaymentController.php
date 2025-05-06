<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Interfaces\PaymentGatewayInterface;
use App\Http\Requests\PaymentRequest;
use App\Service\PaymobService;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceOrder;
use StdClass;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Events\StoreOrderPlaces;
use App\Events\OrderPlaced;
use Illuminate\Support\Str;
use DB;
class PaymentController extends Controller
{
    // protected PaymentGatewayInterface $paymentGateway;

    // public function __construct(PaymentGatewayInterface $paymentGateway)
    // {
    //     $this->paymentGateway = $paymentGateway;
    // }


    protected $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }


    public function paymentProcess(PaymentRequest $orderRequest)
    {

        $method = false ? 'cashOrder' : 'createPaymentIntention';
        $data=$this->paymobService->$method($orderRequest->validated());
        return response()->json(['status'=>true,'message'=>'succes','data'=>$data]);
    }


    // public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    // {
    //     $response = $this->paymentGateway->callBack($request);
    //     if ($response) {
    //         return redirect()->route('payment.success');
    //     }
    //     return redirect()->route('payment.failed');
    // }


//     public function getPaymentToken( $token, $payment_data,$amount_cents)
// {
//     // Payment amount in cents
//     $value = $amount_cents; // Ensure amount is multiplied by 100 for Paymob

//     // Billing Data
//     $billingData = [
//         "apartment" => "N/A",
//         "email" => 'aa0368048@gmail.com',
//         "floor" => "N/A",
//         "first_name" => 'abdu',
//         "street" => "N/A",
//         "building" => "N/A",
//         "phone_number" =>"01276549343"  ??"0000",
//         "shipping_method" => "PKG",
//         "postal_code" => "N/A",
//         "city" => "N/A",
//         "country" => "N/A",
//         "last_name" => "ali",
//         "state" => "N/A",
//     ];

//     // Payment Key Request Data
//     $data = [
//         "auth_token" => $token,
//         "amount_cents" => round($value), // Ensure this is an integer
//         "expiration" => 3600, // 1-hour expiration
//         "order_id" => 55,
//         "billing_data" => $billingData,
//         "shipping_data" => $billingData,
//         "currency" => "EGP",
//         "integrations" =>[4924031, 4065764],
//         "integration_id" => 4924031, // Provide a SINGLE integration ID here//4884799
//     ];
// DB::table('response_text')->insert(['text'=>json_encode($data)]);
// // dd($data);
//     // Send request to Paymob API
//     $response = $this->cURL('https://accept.paymob.com/api/acceptance/payment_keys', $data);
// // return $response;
//     // Debug or log the response
//     if (isset($response->token)) {
//         return $response->token; // Return the payment token if successful
//     } else {
//         // Log the error response
//         Log::error('Paymob Error', ['response' => $response]);
//         throw new \Exception('Paymob payment token generation failed: ' . json_encode($response));
//     }
// }


    // protected function cURL($url, $json)
    // {
    //     // Create curl resource
    //     $ch = curl_init($url);

    //     // Request headers
    //     $headers = array();
    //     $headers[] = 'Content-Type: application/json';

    //     // Return the transfer as a string
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     // $output contains the output string
    //     $output = curl_exec($ch);

    //     // Close curl resource to free up system resources
    //     curl_close($ch);
    //     return json_decode($output);
    // }

    
    public function payment_callback(Request $request){
        $data=$request->all();
        if($data){
                $order=Order::where('pay_order_id',$data['order'])->first(); 
            if($data['success']==true||$data['success']=="true"){
                $order->update(['payment_status'=>'accepted','show_ord'=>1]);
                $url = 'https://kasool.net/api/navigat/?id=' . $request->id . '&success='. $request->success;
                $service=Service::where('id',$order->service_id)->first();
                $total_value=(float)$order->total_price-(float)$order->delivery_cost;
                $companyRatio = ($service->service_ratio / 100) * $total_value;
                  $existingServiceOrder = ServiceOrder::where('order_id', $order->id)->first();
            if (!$existingServiceOrder) {
                $serv_order = ServiceOrder::create([
                    'service_id' => $order->service_id,
                    'order_id' => $order->id,
                    'discount_price'=>$order->discount_price,
                    'order_value' => $total_value,
                    'pay_method' => 'online',
                    'company_ratio' => $companyRatio
                ]);
                $service_money = $total_value - $companyRatio;
            $service->update(['money' => (float) $service->money + (float) $service_money]);
  event(new OrderPlaced($order,$order->branch_id));
            event(new StoreOrderPlaces($order,$order->service_id));
            }

            

            return view('payment',compact('order','url')); 
                // return $order;
            }
             else {
                $update=$order->update(['status'=>'rejected']);
                $url = 'https://kasool.net/api/navigat/?id=' . $request->id . '&success='. $request->success;
                return view('payment',compact('order','url'));
                
                
                // $order=Order::where('payment_id')->update(['status'=>'rejected']);
            }
        }
    }

    public function success()
    {
        return 'Payment Success';
    }
    public function failed()
    {
        return 'Payment Failed';
    }
}
