<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DiscountCouponController extends Controller
{
    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createCoupon(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discount_amount' => 'required|numeric|min:1',
            'expires_at' => 'required|date|after:now',
            'contact' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => $validator->errors()]);
        }

        $user = User::where('email', $request->contact)->orWhere('phone', $request->contact)->first();
        if (!$user) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_NOT_FOUND, ['error' => 'User not found.']);
        }

        $couponCode = strtoupper(Str::random(8)); // رمز الكوبون
        DiscountCoupon::create([
            'code' => $couponCode,
            'discount_amount' => $request->discount_amount,
            'expires_at' => Carbon::parse($request->expires_at),
            'user_id' => $user->id,
        ]);

        $message = "Your discount coupon code is: {$couponCode}. It expires on: {$request->expires_at}.";

        return ApiResponse::apiResponse(JsonResponse::HTTP_OK, 'Discount coupon created and sent successfully.', ['coupon' => $couponCode]);
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => $validator->errors()]);
        }

        $coupon = DiscountCoupon::where('code', $request->code)->first();

        if (!$coupon) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_NOT_FOUND, ['error' => 'Coupon not found.']);
        }

        if (Carbon::now()->greaterThan($coupon->expires_at)) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => 'Coupon has expired.']);
        }

        if ($coupon->used) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => 'Coupon has already been used.']);
        }

        return ApiResponse::apiResponse(JsonResponse::HTTP_OK, 'Coupon is valid.', ['discount_amount' => $coupon->discount_amount]);
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function useCoupon(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => $validator->errors()]);
        }

        $coupon = DiscountCoupon::where('code', $request->code)->first();

        if (!$coupon) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_NOT_FOUND, ['error' => 'Coupon not found.']);
        }

        if (Carbon::now()->greaterThan($coupon->expires_at)) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => 'Coupon has expired.']);
        }

        if ($coupon->used) {
            return ApiResponse::apiResponse(JsonResponse::HTTP_BAD_REQUEST, ['error' => 'Coupon has already been used.']);
        }

        $coupon->update(['used' => true]);

        return ApiResponse::apiResponse(JsonResponse::HTTP_OK, 'Coupon applied successfully.', ['discount_amount' => $coupon->discount_amount]);
    }
}
