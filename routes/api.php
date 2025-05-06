<?php

use App\Http\Controllers\Api\AddController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DiscountCouponController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\GoogleController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\Su0bCategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\AsksController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

// =====================================================  Admin routes  ==================================================

Route::prefix('admin')->group(function () {
    Route::post('register', [AdminController::class, 'register']);
    Route::post('login', [AdminController::class, 'login']);

    Route::middleware(['auth:admin-api', 'verified'])->group(function () {
        Route::get('profile', [AdminController::class, 'showProfile']);
        Route::put('update-profile', [AdminController::class, 'updateProfile']);
        Route::post('reset-password', [AdminController::class, 'resetPassword']);
        Route::post('logout', [AdminController::class, 'logout']);
    });
});
Route::get('verify-email/{verification_code}', [UserController::class, 'verifyEmail'])->name('custom.verification.verify');

// =====================================================  User routes  ==================================================

Route::prefix('user')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('service_login', [UserController::class, 'service_login']);
    Route::get('delete-account', [UserController::class, 'delete_account']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
        Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('update-profile', [UserController::class, 'update']);
        Route::post('logout', [UserController::class, 'logout']);
    });

    // Route::get('password/reset-form', function () {
    //     return view('password_reset');
    // })->name('password.reset-form');    
    Route::post('password/reset', [UserController::class, 'resetPassword']);
    Route::post('check_code', [UserController::class, 'check_code']);
    Route::post('password/change', [UserController::class, 'changePassword']);//->middleware('auth:sanctum')

});

Route::prefix('user')->group(function () {
    Route::post('send-otp', [OtpController::class, 'sendOtp']);
    Route::post('verify-otp', [OtpController::class, 'verifyOtp']);
    Route::post('reset-password', [OtpController::class, 'resetPassword']);
});


// ========================================== Categories ===============================================

Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

// ========================================== Sub Categories ===============================================

Route::get('sub-categories', [SubCategoryController::class, 'index']);
Route::get('sub-categories/{id}', [SubCategoryController::class, 'show']);

// ========================================== Offers ===============================================

Route::get('offers', [OfferController::class, 'index']);
Route::get('offers/{id}', [OfferController::class, 'show']);

// ========================================== products ===============================================

    Route::post('services_store', [ServiceController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::apiResource('sizes', SizeController::class);

    // ========================================== services ===============================================

    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{id}', [ServiceController::class, 'show']);
    Route::put('services/{id}', [ServiceController::class, 'update']);
    
    // ========================================== adds ===============================================
    
    Route::get('adds', [AddController::class, 'index']);
    Route::get('adds/{id}', [AddController::class, 'show']);
    Route::post('/adds', [AddController::class, 'store']);
});

// ========================================== favorites ===============================================

Route::middleware('auth:sanctum')->group(function () {
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{favoriteId}', [FavoriteController::class, 'destroy']);
    Route::delete('/favorites/products/{productId}', [FavoriteController::class, 'removeProducts']);
    Route::delete('/favorites/services/{ServiceId}', [FavoriteController::class, 'removeservices']);

    // ========================================== Cart ===============================================

    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::delete('cart/{cart_item}', [CartController::class, 'destroy']);
    Route::delete('cart/product/{product_id}', [CartController::class, 'removeProduct']);
    Route::delete('cart/add/{add_id}', [CartController::class, 'removeAdd']);
    Route::delete('cart/products', [CartController::class, 'removeProducts']);
    Route::delete('cart/adds', [CartController::class, 'removeAdds']);

    // ========================================== Orders ===============================================
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('{order}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
       
        Route::put('{order}', [OrderController::class, 'update']);
        Route::delete('{order}', [OrderController::class, 'destroy']);
    });
     Route::get('store_history_orders', [OrderController::class, 'store_history_orders']);
 Route::get('resturant_orders', [OrderController::class, 'resturant_orders']);
 Route::get('user_orders', [OrderController::class, 'user_orders']);
 Route::get('order_items/{id}', [OrderController::class, 'order_items']);
 Route::get('branch_orders/{id}', [OrderController::class, 'branch_orders']);
 Route::get('store_orders', [OrderController::class, 'store_orders']);

    
    // ========================================== User Or services Stories =====================================================

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('stories', StoryController::class);
        Route::get('stories/{id}', [StoryController::class, 'show']);
    });

    // ========================================== coupons =====================================================

    Route::prefix('coupons')->group(function () {
        Route::post('create', [DiscountCouponController::class, 'createCoupon']);
        Route::post('validate', [DiscountCouponController::class, 'validateCoupon']);
        Route::post('use', [DiscountCouponController::class, 'useCoupon']);
    });

});


Route::get('/payments/verify/{payment?}', [FrontController::class, 'payment_verify'])->name('payment-verify');


// paymob routes
Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::any('payment_callback', [PaymentController::class, 'payment_callback']);
Route::any('payment/callback', [OrderController::class, 'callBack']);
// 
Route::controller(GoogleController::class)->group(function () {
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});



Route::group([
    'prefix'=>'branches',
    ],function(){
    Route::get('get_store_branch',[BranchController::class,'get_store_branch']);
Route::post('add_new_branch',[BranchController::class,'add_new_branch']);
Route::post('update_branch/{id}',[BranchController::class,'update_branch']);
Route::get('delete_one/{id}',[BranchController::class,'delete_one']);
Route::post('branch_login',[BranchController::class,'branch_login']);
Route::get('branch_profile',[BranchController::class,'branch_profile']);
});

// Route::group(function(){
// // Route::get('test',function(){
// //   return 'test'; 
// // });
// });
Route::get('get_store_images',[ImagesController::class,'get_store_images']);
Route::post('update_image/{id}',[ImagesController::class,'update_image']);
Route::get('delete_one/{id}',[ImagesController::class,'delete_one']);
Route::post('add_new_image',[ImagesController::class,'add_new_image']);

Route::get('get_all_asks',[AsksController::class,'index']);
     
Route::get('terms_conditions',[SettingsController::class,'terms_conditions']);
Route::get('settings',[SettingsController::class,'settings']);
Route::post('make_report',[ReportsController::class,'make_report']);


Route::get('service_subcats',[ServiceController::class,'service_subcats']);
Route::get('service_statistics',[ServiceController::class,'service_statistics']);
Route::get('change_open_status',[ServiceController::class,'change_open_status']);
Route::get('service_profile',[ServiceController::class,'service_profile']);
Route::post('service_update_profile',[ServiceController::class,'service_update_profile']);


Route::get('service_products',[ProductController::class,'service_products']);
Route::post('add_new_product',[ProductController::class,'add_new_product']);
Route::post('update_one_product/{id}',[ProductController::class,'update_one_product']);
Route::get('change_product_status/{id}',[ProductController::class,'change_product_status']);
Route::get('delete_product/{id}',[ProductController::class,'delete_product']);



Route::get('get_store_offers',[OfferController::class,'get_store_offers']);
Route::post('service_add_new_offer',[OfferController::class,'service_add_new_offer']);
Route::post('service_update_offer/{id}',[OfferController::class,'service_update_offer']);
Route::get('service_delete_offer/{id}',[OfferController::class,'service_delete_offer']);

Route::get('offer_details/{id}',[OfferController::class,'offer_details']);
Route::get('delete_offer_image/{id}',[OfferController::class,'delete_offer_image']);


 Route::post('service_change_status', [OrderController::class, 'service_change_status']);


Route::get('delivery_wallets',[OrderController::class,'delivery_wallets']);
Route::get('delivery_history_orders',[OrderController::class,'delivery_history_orders']);


Route::group([
    'prefix'=>'addons',
],function(){
    Route::get('service_adds',[AddController::class,'service_adds']);
    Route::post('service_add_new',[AddController::class,'service_add_new']);
        Route::post('service_update_add/{id}',[AddController::class,'service_update_add']);
        Route::get('delete_addon/{id}',[AddController::class,'delete_addon']);


});

Route::post('calc_delivery_cost/{id}',[CartController::class,'calc_delivery_cost']);

Route::post('update_fcm_token',[UserController::class,'update_fcm_token']);
Route::group([
    'prefix'=>'deliveries',
],function(){
    Route::post('login',[DeliveryController::class,'login']);
    Route::get('delivery_orders',[DeliveryController::class,'delivery_orders']);
    Route::post('change_order_status/{id}',[DeliveryController::class,'change_order_status']);
        Route::get('profile',[DeliveryController::class,'profile']);

    Route::get('assign_order',[OrderController::class,'assign_order']);
});


Route::post('use_coupon',[CouponController::class,'use_coupon']);
    Route::post('global_search',[ProductController::class,'global_search']);