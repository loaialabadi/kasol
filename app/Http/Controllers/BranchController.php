<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Twilio\Rest\Client;
use App\Models\Branch;
use Laravel\Sanctum\PersonalAccessToken;
// use Illuminate\Validation\ValidationException;

class BranchController extends Controller
{
    public function get_store_branch(Request $request){
        $baseUrl="https://kasool.net/storage";
        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Token Nedded'],203);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $brans=Branch::where('service_id',$accessToken->tokenable_id)->get()->map(function ($branch) use ($baseUrl) {
            // return $branch->image;
    $branch->image = $baseUrl . '/' . ltrim($branch->image, '/'); // Ensure proper URL formatting
    return $branch;
});
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data'=>$brans,
            ],200);
    }
    public function add_new_branch(Request $request){
        // $user=Auth::user();
        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Token Nedded'],203);
        }
             $token = explode(' ', $request->header('Authorization'))[1];

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
                // 'service_id' => 'required',
                'name' => 'required',
                'user_name' => 'required|unique:branches,user_name',
                'image' => 'nullable|image',
                'address' => 'nullable',
                'phone' => 'nullable',
                'start_work_date' => 'nullable',
                'end_work_date' => 'nullable',
                'password' => 'required',
                'lat' => 'required',
                'long' => 'required',
            ]);
        $data=[];
        $imagePath=null;
        if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('branch', 'public');
                $data['image_path'] = $imagePath;
            }
        // return $accessToken->tokenable_id;
        $data['service_id']=$accessToken->tokenable_id;
        $data['image']=$imagePath;
        $data['address']=$request->address;
        $data['phone']=$request->phone;
        $data['start_work_date']=$request->start_work_date;
        $data['end_work_date']=$request->end_work_date;
        $data['lat']=$request->lat;
        $data['name']=$request->name;
        $data['user_name']=$request->user_name;
        $data['long']=$request->long;
        $data['password']=Hash::make($request->password);
        // return $data;
        $new_bran=Branch::create($data);
        if($new_bran){
            return response()->json(
                [
                     'success' => true,
            'message' => 'Success To Add',
            'data' => [

            ]
                ]
                );
        }
    }

    public function update_branch(Request $request){
        $id=request('id');
        $branch=Branch::where('id',$id)->first();
        // $user=Auth::user();
             $token = explode(' ', $request->header('Authorization'))[1];

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
                // 'service_id' => 'required',
    'user_name' => 'required|unique:branches,user_name,'.$id,
    'name' => 'required',
    'image' => 'nullable',
                'address' => 'nullable',
                'phone' => 'nullable',
                'start_work_date' => 'nullable',
                'end_work_date' => 'nullable',
                'password' => 'nullable',
                'lat' => 'required',
                'long' => 'required',
            ]);
        $data=[];
        $imagePath=null;
        if($request->password){
             $data['password']=Hash::make($request->password);
        }
        if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('branch', 'public');
                $data['image'] = $imagePath;
            }
        // return $accessToken->tokenable_id;
        $data['service_id']=$accessToken->tokenable_id;
        // $data['image']=$imagePath;
        $data['address']=$request->address;
        $data['phone']=$request->phone;
        $data['start_work_date']=$request->start_work_date;
        $data['end_work_date']=$request->end_work_date;
        $data['user_name']=$request->user_name;
        $data['lat']=$request->lat;
        $data['name']=$request->name;
        $data['long']=$request->long;

        // return $data;
        $up_bran=Branch::where('id',$id)->update($data);
        if($up_bran){
            return response()->json(
                [
                     'success' => true,
            'message' => 'Success To Update',
            'data' => [

            ]
                ],200
                );
        }
        return response()->json([
                     'success' => false,
            'message' => 'Faild To Update',
            'data' => [

            ]   ],203);
    }

    public function branch_login(Request $request)
{
    $credentials = $request->validate([
        'user_name' => 'required|string',
        'password' => 'required|string',
        'fcm_token' => 'required|string',
        
    ]);

    // Retrieve the user based on the name
    $user = Branch::where('user_name', $credentials['user_name'])->first();

    // Validate if user exists and password matches
    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        throw ValidationException::withMessages([
            'user_name' => ['The branch credentials are incorrect.'],
        ]);
    }

    // Create token based on the authenticated entity
    $token = $user->createToken('auth_token')->plainTextToken;
$user->update(['fcm_token'=>$request->fcm_token]);
    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
        'type' => 'user', // Indicate the entity type
    ]);
}
    public function delete_one(Request $request){
        $id=request('id');
        //   return $id;
           $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);

        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $branch=Branch::where('id',$id)->first();
        // return $branch;
        if($branch->service_id!=$accessToken->tokenable_id){
              return response()->json(
                [
                     'success' => false,
            'message' => 'Unaut',
            'data' => [

            ]
                ],401
                );
        }
        $del=$branch->delete();
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
            'data' => [

            ]
                ],203
                );
        }
    }
    
    public function branch_profile(Request $request){
//       $user = auth()->guard('branches')->user();
// return $user;

        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Token Nedded'],203);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $branch=Branch::where('id',$accessToken->tokenable_id)->first([
            'name',
        'address',
        'phone',
        'image',
        'start_work_date',
        'end_work_date',
        'lat',
        'long',
        'id',
        'service_id',
        
            ]);
        return response()->json(['status'=>true,'message'=>'','data'=>$branch]);
    }


    // public function branch_login(Request $request){
    //     $validated = $request->validate([
    //         'name'=>'required|exists:branches',
    //         'password'=>'required'
    //     ]);

    //     $branch=Branch::where('name',$validated['name'])->first();

    //     if (
    //     (!$user || !Hash::check($credentials['password'], $user->password)) &&
    //         (!$service || !Hash::check($credentials['password'], $service->password))
    //     ) {
    //         throw ValidationException::withMessages([
    //             'phone' => ['The provided credentials are incorrect.'],
    //         ]);
    //     }

    // }

}
