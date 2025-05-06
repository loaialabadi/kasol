<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\AsksController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\SubcategoriesController;
// use App\Http\Controllers\Api\SubcategoriesController as SubCats;

use App\Http\Controllers\Dashboard;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiftController;
Route::get('/', [Dashboard::class,'index']);

Route::get('/dashboard', [Dashboard::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('dashboard',[Dashboard::class,'index'])->name('dashboard');

Route::get('permissions_page',[PermissionController::class,'permissions_page'])->name('permissions_page');
Route::get('add_new_permission',[PermissionController::class,'add_new_permission']);
Route::post('store_new_permission',[PermissionController::class,'store_new_permission'])->name('store_new_permission');
// Route::get('/fresh', function () {Artisan::call('migrate:refresh --seed');});
Route::get('update_permission/{id}',[PermissionController::class,'update_permission'])->name('update_permission');
Route::post('edit_permission',[PermissionController::class,'edit_permission'])->name('edit_permission');
Route::get('delete_permission/{id}',[PermissionController::class,'delete_permission'])->name('delete_permission_main');
// Route::get('delete_permission/{id}',[PermissionController::class,'delete_permission'])->name('delete_permission');

Route::group([
    'prefix'=>'rules',
],function(){
    Route::get('rules_page',[RulesController::class,'roles_page'])->name('roles_page');
    Route::get('add_new_role',[RulesController::class,'add_new_role']);
    Route::post('store_new_role',[RulesController::class,'store_new_role'])->name('store_new_role');
    Route::get('update_role/{id}',[RulesController::class,'update_role'])->name('update_role');
    Route::post('edit_role',[RulesController::class,'edit_role'])->name('edit_role');

    Route::get('delete_role/{id}',[RulesController::class,'delete_role'])->name('delete_role');
    Route::get('roles_pemissions/{id}',[RulesController::class,'roles_pemissions'])->name('roles_pemissions');
    Route::get('assign_permission_page/{id}',[RulesController::class,'assign_permission_page'])->name('assign_permission_page');
    Route::post('store_assigned_permission',[RulesController::class,'store_assigned_permission'])->name('store_assigned_permission');
    Route::get('delete_permission/{role_id}/{permission_id}', [RulesController::class, 'delete_permission'])->name('delete_permission');
    // Route::get('/rules/delete_permission/{role_id}/{permission_id}', [PermissionController::class, 'destroy'])->name('delete_permission');
});



Route::group([
    'prefix'=>'admins',
],function(){
    Route::get('admins_page',[AdminController::class,'admins_page'])->name('admins_page');
    Route::get('add_new_admin',[AdminController::class,'add_new_admin'])->name('add_new_admin');
    Route::post('store_new_admin',[AdminController::class,'store_new_admin'])->name('store_new_admin');
    Route::get('delete_admin/{id}',[AdminController::class,'delete_admin'])->name('delete_admin');
    Route::get('update_admin_page/{id}',[AdminController::class,'update_admin_page'])->name('update_admin_page');
    Route::post('store_update_admin',[AdminController::class,'store_update_admin'])->name('store_update_admin');
    Route::get('admin_log',[AdminController::class,'admin_log'])->name('admin_log');
});

Route::get('test_change',function(){
return 'rere';
});


Route::group([
    'prefix'=>'categories'
],function(){
    Route::get('categories_page',[CategoryController::class,'categories_page'])->name('categories_page');
    Route::get('add_new_category',[CategoryController::class,'add_new_category'])->name('add_new_category');
    Route::get('update_category/{id}',[CategoryController::class,'update_category'])->name('update_category');
    Route::post('store_new_category',[CategoryController::class,'store_new_category'])->name('store_new_category');
    Route::get('change_category_status/{id}',[CategoryController::class,'change_category_status'])->name('change_category_status');
    Route::get('delete_categories/{id}',[CategoryController::class,'delete_categories'])->name('delete_categories');
    Route::get('search', [CategoryController::class, 'searchCategories'])->name('search_categories');
    Route::get('category_subcategories/{id}', [CategoryController::class, 'category_subcategories'])->name('category_subcategories');
    Route::post('store_update_category', [CategoryController::class, 'store_update_category'])->name('store_update_category');
});

Route::group([
    'prefix'=>'subcategories'
],function(){
    Route::get('add_new_subcategory/{id}',[SubcategoriesController::class,'add_new_subcategory'])->name('add_new_subcategory');
    Route::post('store_new_subcategory',[SubcategoriesController::class,'store_new_subcategory'])->name('store_new_subcategory');
    Route::get('change_subcategory_status',[SubcategoriesController::class,'change_subcategory_status'])->name('change_subcategory_status');
    Route::get('delete_subcategory/{id}',[SubcategoriesController::class,'delete_subcategory'])->name('delete_subcategory');
    Route::get('update_subcategory/{id}',[SubcategoriesController::class,'update_subcategory'])->name('update_subcategory');
    Route::post('store_update_subcategory/{id}',[SubcategoriesController::class,'store_update_subcategory'])->name('store_update_subcategory');
    Route::get('search_subcategory/{id}',[SubcategoriesController::class,'search_subcategory'])->name('search_subcategory');
});

Route::get('prod_sizes/{id}',[ProductController::class,'prod_sizes'])->name('prod_sizes');
Route::get('assign_size/{id}',[ProductController::class,'assign_size'])->name('assign_size');
Route::post('store_assigned_size/{id}',[ProductController::class,'store_assigned_size'])->name('store_assigned_size');
Route::post('unassigned_size/{id}/{product_id}',[ProductController::class,'unassigned_size'])->name('unassigned_size');
Route::get('update_size/{size_id}/{product_id}',[ProductController::class,'update_size'])->name('update_size');
Route::put('store_update_size/{size_id}/{product_id}',[ProductController::class,'store_update_size'])->name('store_update_size');

Route::group([ 
    'prefix'=>'services',
],function(){
    Route::get('services_page',[ServiceController::class,'services_page'])->name('services_page');
    Route::get('web_service_statistics/{id}',[ServiceController::class,'web_service_statistics'])->name('web_service_statistics');
    Route::post('money_trans/{id}',[ServiceController::class,'money_trans'])->name('money_trans');
    Route::get('service_branch/{id}',[ServiceController::class,'service_branch'])->name('service_branch');
    Route::get('delete_branch/{id}',[ServiceController::class,'delete_branch'])->name('delete_branch');
    Route::post('store_new_branch/{id}',[ServiceController::class,'store_new_branch'])->name('store_new_branch');
    Route::get('update_branch/{id}',[ServiceController::class,'update_branch'])->name('update_branch');
    Route::post('store_update_branch/{id}',[ServiceController::class,'store_update_branch'])->name('store_update_branch');
    Route::get('add_new_branch/{id}',[ServiceController::class,'add_new_branch'])->name('add_new_branch');
    Route::get('change_service_status',[ServiceController::class,'change_service_status'])->name('change_service_status');
    Route::get('update_serv_deliv/{id}',[ServiceController::class,'update_serv_deliv'])->name('update_serv_deliv');
    Route::get('service_products/{id}',[ServiceController::class,'service_products'])->name('service_products');
    Route::get('delete_product/{id}',[ServiceController::class,'delete_product'])->name('delete_product');
    Route::get('update_product/{id}',[ServiceController::class,'update_product'])->name('update_product');
    Route::post('up_prod_file/{id}',[ServiceController::class,'upload_excel_products'])->name('up_prod_file');
    Route::put('update_serv_prod/{id}',[ServiceController::class,'update_serv_prod'])->name('update_serv_prod');
    Route::get('add_new_ser_prod/{id}',[ServiceController::class,'add_new_ser_prod'])->name('add_new_ser_prod');
    Route::post('store_serv_prod/{id}',[ServiceController::class,'store_serv_prod'])->name('store_serv_prod');
    Route::get('add_new_service',[ServiceController::class,'add_new_service'])->name('add_new_service');
    Route::get('search_services',[ServiceController::class,'search_services'])->name('search_services');
    Route::get('delete_services/{id}',[ServiceController::class,'delete_services'])->name('delete_services');
    Route::get('change_service_status/{id}',[ServiceController::class,'change_service_status'])->name('change_service_status');
    Route::post('store_new_service',[ServiceController::class,'store_new_service'])->name('store_new_service');
    Route::get('update_service/{id}',[ServiceController::class,'update_service'])->name('update_service');
    Route::post('store_update_service/{id}',[ServiceController::class,'store_update_service'])->name('store_update_service');
    Route::get('show_service/{id}',[ServiceController::class,'show_service'])->name('show_service');
    Route::get('service_offers/{id}',[ServiceController::class,'service_offers'])->name('service_offers');
});
Route::group(['prefix'=>'offers'],function(){
    Route::get('search_offer/{id}',[OfferController::class,'search_offer'])->name('search_offer');
    Route::get('add_new_offer/{id}',[OfferController::class,'add_new_offer'])->name('add_new_offer');
    Route::get('delete_offer/{id}',[OfferController::class,'delete_offer'])->name('delete_offer');
    Route::get('change_offer_status/{id}',[OfferController::class,'change_offer_status'])->name('change_offer_status');
    Route::get('update_offer/{id}',[OfferController::class,'update_offer'])->name('update_offer');
    Route::post('store_new_offer',[OfferController::class,'store_new_offer'])->name('store_new_offer');
    Route::post('store_update_offer',[OfferController::class,'store_update_offer'])->name('store_update_offer');
    Route::DELETE('delete_offer_image/{imageId}',[OfferController::class,'deleteImage'])->name('delete_offer_image');
});


Route::group([
    'prefix' => 'auth',
], function () {
    Route::get('/login_page', [AdminController::class, 'login_page'])->name('login');
    Route::post('/login', [AdminController::class, 'admin_login'])->name('login_action');
});

Route::group([
    'prefix'=>'asks'
],function(){
    Route::get('asks_page',[AsksController::class,'asks_page'])->name('asks_page');
    Route::get('add_new_ask',[AsksController::class,'add_new_ask'])->name('add_new_ask');
    Route::get('update_ask/{id}',[AsksController::class,'update_ask'])->name('update_ask');
    Route::get('delete_ask/{id}',[AsksController::class,'delete_ask'])->name('delete_ask');
    Route::post('store_new_ask',[AsksController::class,'store_new_ask'])->name('store_new_ask');
    Route::post('store_update_ask',[AsksController::class,'store_update_ask'])->name('store_update_ask');
});


Route::group([
    'prefix'=>'settings'
],function(){
    Route::get('settings_page',[SettingsController::class,'settings_page'])->name('settings_page');
    Route::post('update_setting',[SettingsController::class,'update_setting'])->name('update_setting');
});

Route::group([
    'prefix'=>'reports'
],function(){
    Route::get('reports_page',[ReportsController::class,'reports_page'])->name('reports_page');
    Route::get('delete_report/{id}',[ReportsController::class,'delete_report'])->name('delete_report');
});

Route::group([
    'prefix'=>'orders',
],function(){
    Route::get('admin_orders',[OrderController::class,'admin_orders'])->name('orders');
    Route::get('search_orders',[OrderController::class,'search_orders'])->name('search_orders');
    Route::get('delete_orders/{id}',[OrderController::class,'delete_orders'])->name('delete_orders');
    Route::get('change_order_status/{id}',[OrderController::class,'change_order_status'])->name('change_order_status');
    Route::get('/change_order_status', [OrderController::class, 'changeOrderStatus'])->name('change_order_status');
    Route::get('order_details/{id}', [OrderController::class, 'order_details'])->name('order_details');
    Route::post('assign-delivery', [OrderController::class, 'assign_delivery'])->name('assign_delivery');
});

Route::group(['prefix'=>'gifts'],function(){
    Route::get('gifts_page',[GiftController::class,'gifts_page'])->name('gifts_page');
    Route::get('add_new_gift',[GiftController::class,'add_new_gift'])->name('add_new_gift');
    Route::post('store_new_gift',[GiftController::class,'store_new_gift'])->name('store_new_gift');
    Route::get('change_gift_status/{id}',[GiftController::class,'change_gift_status'])->name('change_gift_status');
    Route::get('update_gift/{id}',[GiftController::class,'update_gift'])->name('update_gift');
    Route::get('delete_gifts/{id}',[GiftController::class,'delete_gifts'])->name('delete_gifts');
    Route::get('search_gifts',[GiftController::class,'search_gifts'])->name('search_gifts');
    Route::post('store_update_gift',[GiftController::class,'store_update_gift'])->name('store_update_gift');
    Route::get('gift_users/{id}',[GiftController::class,'gift_users'])->name('gift_users');
});

Route::group([
        'prefix'=>'coupons',
        
    ],function(){
    Route::get('/',[CouponController::class,'coupons'])->name('coupons');
    Route::get('add_new',[CouponController::class,'new_coupon_page'])->name('new_coupon_page');
    Route::post('store_new_coupon',[CouponController::class,'store_new_coupon'])->name('store_new_coupon');
    Route::get('delete_coupon/{id}',[CouponController::class,'delete_coupon'])->name('delete_coupon');
    Route::get('update_coupon/{id}',[CouponController::class,'update_coupon'])->name('update_coupon');
    Route::post('store_update_coupon/{id}',[CouponController::class,'store_update_coupon'])->name('store_update_coupon');
    Route::get('change_cop_status/{id}',[CouponController::class,'change_cop_status'])->name('change_cop_status');
});

Route::group([
    'prefix'=>'deliveries',
],function(){
    Route::get('deliveries_page',[DeliveryController::class,'deliveries_page'])->name('deliveries_page');
    Route::get('add_new_delivery',[DeliveryController::class,'add_new_delivery'])->name('add_new_delivery');
    Route::get('update_delivery/{id}',[DeliveryController::class,'update_delivery'])->name('update_delivery');
    Route::get('delete_delivery/{id}',[DeliveryController::class,'delete_delivery'])->name('delete_delivery');
    Route::post('store_new_deli',[DeliveryController::class,'store_new_deli'])->name('store_new_deli');
    Route::post('store_update_delivery',[DeliveryController::class,'store_update_delivery'])->name('store_update_delivery');
    Route::get('change_del_status/{id}',[DeliveryController::class,'change_del_status'])->name('change_del_status');
});

Route::group([
    'prefix'=>'notifications'
],function(){
Route::get('get_notifications',[NotificationsController::class,'index'])->name('get_notifications');
Route::get('create_notification',[NotificationsController::class,'create_notification'])->name('create_notification');
Route::post('store_notification',[NotificationsController::class,'store_notification'])->name('store_notification');
Route::get('delete_notification/{id}',[NotificationsController::class,'delete_notification'])->name('delete_notification');
});


// require __DIR__.'/auth.php';
