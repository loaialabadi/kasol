<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliLoginRequest;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Resources\DeliveryOrderResource;
use stdClass;
class DeliveryController extends Controller
{
    public function deliveries_page(){
        $deliveries=Delivery::paginate(10);
        return view('Delivery.deliveries',compact('deliveries'));
    }
    public function add_new_delivery(){
        return view('Delivery.add_new_delivery');
    }
    public function store_new_deli(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:deliveries',
            'password' => 'required',
            'phone' => 'required|string|unique:deliveries,phone',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $validatedData = $validator->validated();
        $validatedData['password']= Hash::make($request->password);
        $new=Delivery::create($validatedData);
        if($new){
            return redirect()->route('deliveries_page')->with('success','Success To Add');
        }
            return redirect()->route('deliveries_page')->with('error','Faild To Add');
    }
    public function change_del_status(){
        $id = request('id');
        $category = Delivery::where('id',$id)->first(); // It's more efficient to use find() here instead of where()->first()
// return $category;

        if ($category) {
            // return $category->ban;
            // Toggling the status between 'active' and 'inactive'
            $up = $category->update(['ban' => $category->ban == "active" ? "inactive" : "active"]);

            if ($up) {
                return redirect()->back()->with('success', 'Success To Update Status');
            }

            return redirect()->back()->with('error', 'Failed To Update Status'); // Fixed typo here
        }

        return redirect()->back()->withErrors(['Not Found']);
    }
    public function delete_delivery(){
        $id = request('id');
        $category = Delivery::find($id); // It's more efficient to use find() here instead of where()->first()
// return $category;
        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $up = $category->delete();

            if ($up) {
                return redirect()->back()->with('success', 'Success To Update Delete');
            }

            return redirect()->back()->with('error', 'Failed To Update Delete'); // Fixed typo here
        }

        return redirect()->back()->withErrors(['Not Found']);
    }
    public function update_delivery(){
        $id=request('id');
        $delivery=Delivery::where('id',$id)->first();
        return view('Delivery.update_delivery',compact('delivery'));
    }
    public function store_update_delivery(Request $request){
        $id=$request->id;
        // return $id;
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'email' => 'nullable|unique:users,email,'.$id,
                'password' => 'nullable',
            'phone' => 'required|unique:deliveries,phone,'.$id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $validatedData = $validator->validated();
        // return $validatedData;
        if($request->password){
            $validatedData['password']= Hash::make($request->password);
        }
        else {
            unset($validatedData['password']);
        }
        $up=Delivery::where('id',$id)->update($validatedData);
        if($up){
            return redirect()->route('deliveries_page')->with('success','Success To Update');
        }
        return redirect()->route('deliveries_page')->with('error','Faild To Update');
    }

    public function login(DeliLoginRequest $request){
        $data=$request->validated();
        $phone=$request->phone;
        $delivery=Delivery::where('phone',$phone)->first();
        $delivery->update(['fcm_token'=>$request->fcm_token]);
        if(!$delivery){
            $em=new stdClass();
            return response()->json(['status'=>false,'message'=>'This Account Does Not Exist','data'=>$em],203);
        }
       if (!$delivery || !Hash::check($data['password'], $delivery->password)) {
    throw ValidationException::withMessages([
        'user_name' => ['The branch credentials are incorrect.'],
    ]);
}
$data=new stdClass();
        $token = $delivery->createToken('auth_token')->plainTextToken;
$data->token=$token;
$data->delivery=$delivery;
        return response()->json(['status'=>true,'message'=>'Login Successful','data'=>$data]);
    }

    public function delivery_orders(Request $request){
        $status=request()->query('status');
        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'unauth'],401);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
// return $accessToken;
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                        'data'=>[],
                    ],401);
        }
        $orders=Order::with('order_items.product','order_items.size','order_items.add','service','user')->where('delivery_id',$accessToken->tokenable_id);
        if($status=='current'){
            $orders->whereIn('status',['pending','shipped','processing','accepted']);
        }
        else {
            $orders->where('status','delivered');
        }
        $orders=$orders->get();
        return response()->json(['status'=>true,'message'=>'Success To Get Data','data'=>DeliveryOrderResource::collection($orders)??[]]);
    }

    public function change_order_status(Request $request){
        $id=request('id');
        $new_status=$request->status;
        $change=Order::where('id',$id)->update(['status'=>$new_status]);
        if($change){
            return response()->json(['status'=>true,'message'=>'Success To Change Status'],200);
        }
        return response()->json(['status'=>false,'message'=>'Faild To Change Status'],200);
    }
    public function profile(Request $request){
        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'unauth'],401);
        }
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        $delivery=Delivery::where('id',$accessToken->tokenable_id)->first();
        return response()->json(['status'=>true,'message'=>'sucess','data'=>$delivery]);
    }
}