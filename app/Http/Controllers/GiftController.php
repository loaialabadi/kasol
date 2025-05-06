<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\GiftsUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class GiftController extends Controller
{
        //

    public function gifts_page(){
        $gifts=Gift::paginate(10);
        return view('gifts.gifts_page',compact('gifts'));
    }
    public function get_gifts(Request $request){
        $gifts=Gift::where('status','active')->get();
        return response()->json(['status'=>true,'message'=>'success','data'=>$gifts]);
    }
    public function give_to_user(Request $request){
        $id=request('id');
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);

        if($accessToken==null){
            return response()->json(['status'=>false,'message'=>'Session Ended Login Again']);
        }
        $data=[];
        $data['user_id']=$accessToken->tokenable_id;
        $data['gift_id']=$id;
        $new_gift=GiftsUser::create($data);
        if($new_gift){
            return response()->json(['status'=>true,'message'=>'Congratulations'],200);
        }
        return response()->json(['status'=>false,'message'=>'unfortunately try again'],200);
    }

    public function my_gifts(Request $request){
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);

        if($accessToken==null){
            return response()->json(['status'=>false,'message'=>'Session Ended Login Again']);
        }
        $gifts_id=GiftsUser::where('user_id',$accessToken->tokenable_id)->pluck('gift_id')->toArray();
        $gifts=Gift::whereIn('id',$gifts_id)->get();
        return response()->json(['status'=>true,'message'=>'success To Get','data'=>$gifts]);
    }
    public function add_new_gift(){
        return view('gifts.add_new_gift');
    }
    public function search_gifts(){

    }

    public function store_new_gift(Request $request){
        $validator = Validator::make($request->all(), [
            'discount' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data=[];
        $data['discount']=$request->discount;
        $data['type']=$request->type;
        // return $data;
        $new_one=Gift::create($data);
        if($new_one){
            return redirect()->route('gifts_page')->with('success','Success To Add');
        }
        return redirect()->route('gifts_page')->with('error','Faild To Add');
    }
    public function change_gift_status(){
        $id = request('id');
        $gift = Gift::find($id); // It's more efficient to use find() here instead of where()->first()

        if ($gift) {
            // Toggling the status between 'active' and 'inactive'
            $up = $gift->update(['status' => $gift->status == 'active' ? 'inactive' : 'active']);

            if ($up) {
                return redirect()->back()->with('success', 'Success To Update Status');
            }

            return redirect()->back()->with('error', 'Failed To Update Status'); // Fixed typo here

        }
    }
    public function update_gift(){
        $id=request('id');
        $gift=Gift::where('id',$id)->first();
        return view('gifts.update_gift',compact('gift'));
    }
    public function store_update_gift(Request $request){
        $validator = Validator::make($request->all(), [
            'discount' => 'required',
            'type' => 'required',
        ]);
        $id=$request->id;

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data=[];
        $data['discount']=$request->discount;
        $data['type']=$request->type;
        $gift=Gift::where('id',$id)->first();
        $up_gift=$gift->update($data);
        if($up_gift){
            return redirect()->route('gifts_page')->with('success','success to update');
        }
        return redirect()->route('gifts_page')->with('error','Faild to update');
    }
    public function delete_gifts(){
        $id=request('id');
        $del=Gift::where('id',$id)->delete();
        if($del){
            return redirect()->back()->with('success','Success To Delete');
        }
        return redirect()->back()->with('error','Faild To Delete');
    }
    public function gift_users(){
        $id=request('id');
        $gift_users_id=GiftsUser::where('gift_id',$id)->pluck('user_id')->toArray();
        $users=User::whereIn('id',$gift_users_id)->paginate(10);
        return view('gifts.gift_users',compact('users'));
    }
}