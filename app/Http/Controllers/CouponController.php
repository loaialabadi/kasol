<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Cart;
use App\Models\CouponUser;
class CouponController{
    public function coupons(){
        $coupons=Coupon::paginate(10);
        return view('coupons.index',compact('coupons'));
    }
    public function new_coupon_page(){
        return view('coupons.new_coupon_page');
    }
public function store_new_coupon(Request $request){
    $validator = Validator::make($request->all(), [
        'code' => 'required|string|unique:coupons,code',
        'value' => 'required|numeric',
        'type'=>'required',
        'limit_use' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();
    $new = Coupon::create($data);

    if ($new) {
        return redirect()->route('coupons')->with('success', 'تمت الإضافة بنجاح');
    }

    return redirect()->route('coupons')->with('error', 'لم تتم الإضافة');
}
public function update_coupon(){
    $id=request('id');
    $coupon=Coupon::where('id',$id)->first();
    return view('coupons.update_page',compact('coupon'));
    return $id;
}
public function delete_coupon(){
    $id=request('id');
    // return $id;
    $del=Coupon::where('id',$id)->delete();
    return redirect()->back()->with('success','تم المسح بنجاح');
}


public function store_update_coupon(Request $request){
    $id=request('id');
    $validator = Validator::make($request->all(), [
        'code' => 'nullable|string|unique:coupons,code,'.$id,
        'value' => 'nullable|numeric',
        'type'=>'nullable',
        'limit_use' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();
    $new = Coupon::where('id',$id)->update($data);

    if ($new) {
        return redirect()->route('coupons')->with('success', 'تم التعديل بنجاح');
    }

    return redirect()->route('coupons')->with('error', 'لم يتم التعديل');
}
public function change_cop_status(){
    $id=request('id');
    $cop=Coupon::where('id',$id)->first();
    $change=$cop->update(['status'=>$cop->status==1?0:1]);
    return redirect()->back()->with('success','تم التغيير بنجاح');
}
    public function update_page(){
        $id=request('id');
        $coupon=Coupon::where('id',$id)->first();
        return view('coupons.update_page',compact('coupon'));
    }
    
    
   public function use_coupon(CouponRequest $request) {
    $token = explode(' ', $request->header('Authorization'))[1] ?? null;
    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken) {
        return response()->json([
            'success' => false,
            'message' => 'إنتهت الجلسه سجل من جديد',
             'original_price' => 0,
        'discount_value' => 0,
        'coupon_id' => 0,
        'final_price' => 0
        ], 401);
    }

    $code = Coupon::where('code', $request->code)->first();
    
    if (!$code) {
        return response()->json(
            ['status' => false, 'message' => 'هذا الكود غير موجود'
            ,
             'original_price' => 0,
        'coupon_id' => 0,
        'discount_value' => 0,
        'final_price' => 0
            ], 203);
    }

    if ($code->much_used >= $code->limit_use) {
        return response()->json([
            'status' => false,
            'message' => 'تم نفاذ كميه استخدام الكود',
              
             'original_price' => 0,
        'discount_value' => 0,
        'coupon_id' => 0,
        'final_price' => 0
        ], 203);
    }

    $check_used_before = CouponUser::where([
        'user_id' => $accessToken->tokenable_id,
        'coupon_id' =>(int) $code->id,
    ])->exists();

    if ($check_used_before) {
        return response()->json([
            'status' => false,
            'message' => 'لقد استخدمت الكود من قبل',
               'original_price' => 0,
        'discount_value' => 0,
        'coupon_id' => 0,
        'final_price' => 0
        ], 203);
    }

    $total_cart_price = Cart::where('user_id', $accessToken->tokenable_id)->sum('total_price');

    $discount_value = 0;
    
    if ($code->type == 'flat') {
        $discount_value = (int)$code->value;
    } elseif ($code->type == 'percentage') {
        $discount_value = ($total_cart_price * $code->value) / 100;
    }

    $discounted_price = max($total_cart_price - $discount_value, 0); // Ensure it doesn't go below 0

    // Save the coupon usage
    // CouponUser::create([
    //     'user_id' => $accessToken->tokenable_id,
    //     'coupon_id' => $code->id,
    // ]);

    // Increment usage count
    $code->increment('much_used');

    return response()->json([
        'status' => true,
        'message' => 'تم تطبيق الكوبون بنجاح',
        'original_price' => (int)$total_cart_price,
        'discount_value' => (int)$discount_value,
        'final_price' => (int)$discounted_price,
        'coupon_id'=>(int)$code->id,
    ], 200);
}

}