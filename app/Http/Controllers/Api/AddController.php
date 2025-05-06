<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddRequest;
use App\Http\Resources\AddResource;
use App\Models\Add;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AddController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            if (!is_numeric($perPage) || $perPage <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'The per_page parameter must be a positive number'
                ], 400);
            }

            if (!is_numeric($page) || $page <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'The page parameter must be a positive number'
                ], 400);
            }

            $adds = Add::paginate($perPage, ['*'], 'page', $page);

            if ($adds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No adds found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Adds fetched successfully',
                'data' => AddResource::collection($adds),
                'pagination' => [
                    'total' => $adds->total(),
                    'per_page' => $adds->perPage(),
                    'current_page' => $adds->currentPage(),
                    'pages_count' => $adds->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching adds',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $add = Add::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Add fetched successfully',
                'data' => new AddResource($add)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Add not found'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the add',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(AddRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('adds', 'public');
                $validatedData['image'] = $imagePath;
            }

            $add = Add::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Add created successfully',
                'data' => new AddResource($add),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while creating the add',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
        public function service_add_new(Request $request){

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


        $rules = [
            'name'             => 'required',
            'image'            => 'required',
            'wight'  => 'required',
            'price'  => 'required',
            'product_id'    => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
            ], 422);
        }
        $validatedData = $validator->validated();
        $validatedData['service_id']=$accessToken->tokenable_id;
        if($request->hasFile('image')){
            $imagePath = $request->hasFile('image') ? $request->file('image')->store('add', 'public') : null;
            $validatedData['image']=$imagePath;
        }
        $new_one=Add::create($validatedData);
        if($new_one){
            return response()->json(['status'=>true,'message'=>'Success To Add'],200);
        }
        return response()->json(['status'=>false,'message'=>'Faild To Add'],200);

    }
    
    
    
    
    
    public function service_update_add(Request $request)
{
    $id=request('id');
    $token = explode(' ', $request->header('Authorization'))[1];
    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken) {
        return response()->json([
            'success' => false,
            'message' => 'Session Ended. Login Again',
        ], 401);
    }

    $rules = [
        'name'      => 'nullable',
        'image'     => 'nullable',
        'wight'     => 'nullable',
        'price'     => 'nullable',
        'product_id'=> 'nullable',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $validatedData = $validator->validated();
    $validatedData['service_id'] = $accessToken->tokenable_id;

    $add = Add::find($id);

    if (!$add) {
        return response()->json([
            'status'  => false,
            'message' => 'Record not found',
        ], 404);
    }

    // Handle image update
    if ($request->hasFile('image')) {
        if ($add->image) {
            Storage::disk('public')->delete($add->image); // Delete old image
        }
        $imagePath = $request->file('image')->store('add', 'public');
        $validatedData['image'] = $imagePath;
    }

    $updated = $add->update($validatedData);

    if ($updated) {
        return response()->json(['status' => true, 'message' => 'Update Successful'], 200);
    }

    return response()->json(['status' => false, 'message' => 'Failed to Update'], 500);
}

    

    public function service_adds(Request $request){
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
        $data=Add::where('service_id',$accessToken->tokenable_id)->get();
        return response()->json(['status'=>true,'message'=>'Success To Get','data'=>$data]);
    }
    
    public function delete_addon(Request $request){
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
        $id=request('id');
        
        $add=Add::find($id);
        
        if($add==null){
            return response()->json(['status'=>true,'message'=>'This Item Not Exist'],203);
        }
        
        if($add->service_id!=$accessToken->tokenable_id){
            return response()->json(['status'=>false,'message'=>'This Item Not For You'],203);
        }
        $del=$add->delete();
        if($del){
            return response()->json(['status'=>true,'message'=>'Success To Delete'],200);
        }
        return response()->json(['status'=>false,'message'=>'Faild To Delete'],200);
    }
}
