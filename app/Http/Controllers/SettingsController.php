<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function settings_page(){
        $settings=Setting::first();
        // return $settings;
        return view('settings.settings_page',compact('settings'));
    }
    //
    public function terms_conditions(){
        $data=Setting::first(['id','terms_and_conds']);
        return response()->json([
            'status'=>true,
            'message'=>'Success',
            'data'=>$data 
        ]);
    }
    public function settings(){
         $data=Setting::first();
        return response()->json([
            'status'=>true,
            'message'=>'Success',
            'data'=>$data 
        ]);
    }
    public function update_setting(Request $request){
        $setting_up=Setting::where('id',1)->update([
            'terms_and_conds'=>$request->terms_and_conds,
            'about_app'=>$request->about_app,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'start_order_price'=>$request->start_order_price,
            'delivery_price'=>$request->delivery_price,
            ]);
            if($setting_up){
                return redirect()->back()->with('success','تم التحديث');
            }
             return redirect()->back()->with('error','لم يتم التحديث');

        // return $request;
    }
}