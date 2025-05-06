<?php

namespace App\Http\Controllers\Api;

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
use App\Models\Service;
use Laravel\Sanctum\PersonalAccessToken;
use stdClass;
class UserController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_verified' => 'nullable|boolean',
            'email_verified_at' => 'nullable|date|default:now()',
            'address' => 'required|string|max:255',
            // 'category_id'=>'nullable',
            'phone' => 'required|string|max:20|unique:users,phone',
            'age' => 'nullable|integer|min:1',
            'gender' => 'nullable|in:male,female,other',
            'verification_method' => 'nullable|in:email,phone',
            'type_role' => 'required|in:customer,vendor,delivery',
            'category_id' => 'nullable|integer|exists:categories,id|required_if:type_role,vendor',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'files.*' => 'nullable|file',
        ]);
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('profile_images', 'public')
            : null;
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? 'other',
            'type_role' => $validated['type_role'],
            'category_id' => $validated['category_id'] ?? null,
            'is_verified' => $validated['is_verified'] ?? 1,
            'image' => $imagePath,
        ]);
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('user_files', 'public');
                $user->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                ]);
            }
        }
        $verificationCode = rand(100000, 999999);
        $user->update(['verification_code' => $verificationCode]);
        if ($validated['verification_method'] === 'email') {
            $user->notify(new CustomVerifyEmail($verificationCode));
        } elseif ($validated['verification_method'] === 'phone' && $user->phone) {
            // $this->sendSmsVerificationCode($user->phone, $verificationCode);
        }
        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Please verify your account.',
            'data' => [
                'user' => $user,
                'image_url' => $imagePath ? asset("storage/$imagePath") : null,
            ]
        ], 201);
    }

    private function sendSmsVerificationCode($phoneNumber, $verificationCode)
    {
        if (!preg_match('/^\+/', $phoneNumber)) {
            $phoneNumber = '+20' . ltrim($phoneNumber, '0');
        }
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');
        $client = new Client($sid, $token);
        $client->messages->create(
            $phoneNumber,
            [
                'from' => $twilioPhoneNumber,
                'body' => "Your verification code is: $verificationCode"
            ]
        );
    }
    
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|numeric',
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->verification_code === (int) $validated['otp']) {
            $user->update(['is_verified' => true]);
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }
    }

    public function verifyEmail($verificationCode)
    {
        $user = User::where('verification_code', $verificationCode)->first();
        if ($user) {
            $user->markEmailAsVerified();
            $user->update(['is_verified' => true]);
            return response()->json([
                'success' => true,
                'message' => 'Email successfully verified.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid verification code.'
        ], 404);
    }

    public function login(Request $request)
    {
            $credentials = $request->validate([
        'phone' => 'required|string',
        'password' => 'required|string',
        'type_role' => 'required|string',
        'fcm_token' => 'required|string',
    ]);

    // Check in the 'users' table
    $user = User::where('phone', $credentials['phone'])->where('type_role',$request->type_role)->first();
if($request->type_role=='customer'){
    if(!$user){
        throw ValidationException::withMessages([
            'phone' => ['The Customer credentials are incorrect.'],
        ]);
    }
}
    // If not found in 'users', check the 'services' table
    $service = null;
    if (!$user) {
        $service = Service::where('phone', $credentials['phone'])->first();//->where('type_role',$request->type_role)
    }

    // Validate password for the respective table
    if (
        (!$user || !Hash::check($credentials['password'], $user->password)) &&
        (!$service || !Hash::check($credentials['password'], $service->password))
    ) {
        throw ValidationException::withMessages([
            'phone' => ['The provided credentials are incorrect.'],
        ]);
    }

    // Create token based on the authenticated entity
    $authenticatedEntity = $user ?? $service;
    $token = $authenticatedEntity->createToken('auth_token')->plainTextToken;
$user->update(['fcm_token'=>$request->fcm_token]);
    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'token' => $token,
        'user' => $authenticatedEntity,
        'type' => $user ? 'user' : 'service', // Indicate the table the entity belongs to
    ]);
    }
   public function service_login(Request $request)
{
    // Validate request
    $credentials = $request->validate([
        'phone' => 'required|string',
        'password' => 'required|string',
        'fcm_token' => 'required|string',
    ]);

    // Find service by phone
    $service = Service::where('phone', $credentials['phone'])->first();

    // Check password validity
    if (!$service || !Hash::check($credentials['password'], $service->password)) {
        throw ValidationException::withMessages([
            'phone' => ['The provided credentials are incorrect.'],
        ]);
    }
    $em=new stdClass();

if($service->status=='inactive'){
    return response()->json([
        'success' => false,
        'message' => 'تواصل مع الدعم لتفعيل الحساب',
        'token' => '',
        'user' => $em, // Returning service entity
        'type' => 'service', // Indicating entity type
    ],401);
}
    // Create authentication token
    $token = $service->createToken('auth_token')->plainTextToken;

    // Update FCM token if provided
    if ($request->has('fcm_token')) {
        $service->update(['fcm_token' => $request->fcm_token]);
    }



    // Return response
    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'token' => $token,
        'user' => $service, // Returning service entity
        'type' => 'service', // Indicating entity type
    ]);
}


    public function profile(Request $request)
    {
          $user = $request->user(); // Get the authenticated user

    return response()->json([
        'success' => true,
        'message' => 'User profile retrieved successfully',
        'user' => $user
    ]);
        return response()->json([
            'success' => true,
            'massage' => 'User profile fetched successfully',
            'user' => new UserResource(Auth::user())
        ]);
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                // 'email' => 'nullable|string|unique:users,email,' . $user->id,
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:12',
                'image' => 'nullable|image|mimes:png,jpg,jpeg',
                'age' => 'nullable|integer|min:1',
                'whatsapp_number' => 'nullable|string|max:20',
                'long' => 'nullable|string',
                'late' => 'nullable|string',
                'gender' => 'nullable|in:male,female,other',
                'type_role' => 'nullable|in:customer,vendor,delevery',
                'category_id' => 'nullable|integer|exists:categories,id',
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $validated['image_path'] = $imagePath;
            }

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => new UserResource($user),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'massage' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function  check_code(Request $request){
        $validated = $request->validate([
            'phone' => 'required',
            'code'=>'required'
        ]);
        $user=User::where('phone',$request->phone)->first();
        if($user==null){
            response()->json([
                'success' => false,
                'message' => 'This Account Does not exist'
            ],401);
        }   
        if($user->verification_code!=$request->code){
            return response()->json([
                'success' => false,
                'message' => 'incorrect Code.'  
            ],410);
        }
        $user->update(['is_verified'=>1]);
        return response()->json([
                'success' => true,
                'message' => 'correct Code.'
            ]);
        
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'phone'=>'required'
        ]);

        // $user = Auth::user();
        $user=User::where('phone',$request->phone)->first();
           if($user==null){
            response()->json([
                'success' => false,
                'message' => 'This Account Does not exist'
            ],401);
        }
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 400);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required',
        ]);
        $user=User::where('phone',$request->phone)->first();
        
        if($user==null){
            response()->json([
                'success' => false,
                'message' => 'This Account Does not exist'
            ],401);
        }
        $verificationCode = rand(100000, 999999);
        
        $update=$user->update(['verification_code' => $verificationCode]);
    if($update){
        return response()->json([
                'success' => true,
                'message' => 'Success To Send.'
            ]);
    }
        $status = Password::sendResetLink($validated);

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'success' => true,
                'message' => 'Password reset link sent.'
            ])
            : response()->json([
                'success' => false,
                'message' => 'Failed to send password reset link.'
            ], 500);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully.'
        ], 200);
    }
    public function update_fcm_token(Request $request){
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
        
        $up_fcm=User::where('id',$accessToken->tokenable_id)->update(['fcm_token'=>$request->fcm_token]);
         return response()->json([
                        'success' => true,
                        'message' => 'Success',
                    ],200); 

    }
    public function delete_account(Request $request){
          if(!request()->header('Authorization')){
              return redirect()->route('login');
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
        $user=User::where('id',$accessToken->tokenable_id)->first();
        if($user){
            $user->delete();
            return response()->json(['status'=>true,'message'=>'تم المسح بنجاح']);
        }
        return response()->json(['status'=>false,'message'=>'الحساب غير موجود'],401);
    }
}
