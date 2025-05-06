<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ImagesController extends Controller
{
    public function add_new_image(Request $request){
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
            return response()->json([
                    'success' => false,
                    'message' => 'Session Ended Login Again',
                ],401);
        }
        $request->validate([
                'image' => 'required|image',
            ]);
        $data=[];
         $data['service_id']=$accessToken->tokenable_id;
        // return $data;
        $imagePath=null;
        if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('offers_image', 'public');
                $data['image_path'] = $imagePath;
            }
        $data['image']=$imagePath;
        $data['service_id']=$accessToken->tokenable_id;
        $new_bran=Images::create($data);
        if($new_bran){
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Success To Add',
                    'data' => []
                ],200
                );
        }
        return response()->json([
            'success' => true,
            'message' => 'Faild To Add',
            'data' => []
        ],203);
    }

    public function update_image(Request $request){
        $id=request('id');
        $token = explode(' ', $request->header('Authorization'))[1];
        $image=Images::where('id',$id)->first();
    
        $accessToken = PersonalAccessToken::findToken($token);
    // return $accessToken;
        // return $user;
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $request->validate([
                'image' => 'required',
            ]);
            
            
            if($image->service_id!=$accessToken->tokenable_id){
              return response()->json(
                [
                     'success' => false,
            'message' => 'Unaut',
            'data' => [

            ]
                ],401
                );
        }
            
            
        $data=[];
        $data['service_id']=$accessToken->tokenable_id;
        // return $data;
        $imagePath=null;
        if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('offers_image', 'public');
                $data['image'] = $imagePath;
            }
        $up_bran=Images::where('id',$id)->update($data);
        if($up_bran){
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Success To Update',
                    'data' => []
                ],200
                );
        }
        return response()->json([
                     'success' => false,
            'message' => 'Faild To Update',
            'data' => [

            ]   ],203);
    }

    public function delete_one(Request $request){
        $id=request('id');
        
                $image=Images::where('id',$id)->first();

        //   return $id;
           $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);

        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $image=Images::where('id',$id)->first();
        
        
        if($image->service_id!=$accessToken->tokenable_id){
              return response()->json(
                [
                     'success' => false,
            'message' => 'Unaut',
            'data' => [

            ]
                ],401
                );
        }

        $del=$image->delete();
        if($del){
            return response()->json(
                [
                    'success' => true,
            'message' => 'Success To Delete',
            'data' => [

            ]
                ],200
                );
        }
        else {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'faild To Update',
                    'data' => []
                ],203
            );
        }
    }
    public function get_store_images(Request $request){
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        // return $accessToken;
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $imgs=Images::where('service_id',$accessToken->tokenable_id)->get(['id','image','service_id']);
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data'=>$imgs,
        ],200);
    }
}