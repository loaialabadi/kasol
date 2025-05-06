<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Service\OtpService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    // protected OtpService $otpService;

    // public function __construct(OtpService $otpService)
    // {
    //     $this->otpService = $otpService;
    // }

    /**
     * إرسال كود OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'nullable',//required|string
            'phone'=>'required',
            'contact_type' => 'nullable',//required|in:email,phone
        ]);

        if ($validator->fails()) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => $validator->errors()]);
        }

        $user = User::where('phone',$request->phone)->first();
        if (!$user) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_NOT_FOUND, ['error' => 'User not found.']);
        }
        $code=rand(100000,999999);
$user->update(['verification_code'=>$code]);
        $lastOtp = OtpCode::where('contact', $request->contact)->latest()->first();
        if ($lastOtp && Carbon::now()->diffInSeconds($lastOtp->created_at) < 30) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_TOO_MANY_REQUESTS, ['error' => 'Please wait before requesting a new OTP.']);
        }

        OtpCode::where('contact', $request->contact)->delete(); // حذف الكود القديم

        $message = '';
        // switch ($request->contact_type) {
        //     case 'email':
        //         $otp = $this->otpService->sendOtpEmail($request->contact, $request->contact_type);
        //         $message = 'OTP sent to your email address';
        //         break;
        //     case 'phone':
        //         $otp = $this->otpService->sendOtpPhone($request->contact, $request->contact_type);
        //         $message = 'OTP sent to your phone number';
        //         break;

        //     default:
        //         return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => 'Invalid contact type.']);
        // }
       return response()->json(['status' => true, 'message' => 'تم الإرسال']);

        // return ApiResponse::apiResponse(JsonResponse::HTTP_OK, $message, []);
    }

    /**
     * التحقق من OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'nullable',
            'otp' => 'required|integer',
        ]);

        $user=User::where('phone',$request->phone)->first();
        if($user==null){
            return response()->json(['status'=>false,'message'=>'This Account Does Not Exist']);
        }
        if($user->verification_code!=$request->otp){
            return response()->json(['status'=>false,'message'=>'Invalied OTP'],410);
        }
        $user->update(['is_verified'=>1,'verification_code'=>null]);
        return response()->json(['status'=>true,'message'=>'Valied Otp']);
        if ($validator->fails()) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => $validator->errors()]);
        }

        $otpRecord = OtpCode::where('contact', $request->contact)->first();

        if (!$otpRecord) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_NOT_FOUND, ['error' => 'OTP not found.']);
        }

        if ($otpRecord->otp !== $request->otp) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => 'Invalid OTP.']);
        }

        if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => 'OTP has expired.']);
        }
        $otpRecord->delete();
        return ApiResponse::apiResponse(JsonResponse::HTTP_OK, 'OTP verified successfully. Please reset your password.');
    }

    /**
     * إعادة تعيين كلمة المرور
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'contact_type' => 'required|in:email,phone',
        ]);

        if ($validator->fails()) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => $validator->errors()]);
        }

        $user = User::where($request->contact_type, $request->contact)->first();
        if (!$user) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_NOT_FOUND, ['error' => 'User not found.']);
        }

        $user->update(['password' => $request->password]); // حذف كود OTP بعد النجاح

        return ApiResponse::apiResponse(JsonResponse::HTTP_OK, 'Password reset successfully.');
    }
}