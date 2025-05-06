<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Requests\ProductActionRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Service;
use App\Models\Offer;
use App\Models\ProductSize;
use App\Models\Size;
use stdClass;
class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated. Please provide a valid token.',
                ], 401);
            }

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

            $serviceId = $request->input('service_id');

            $query = Product::with(['image', 'subCategory', 'sizes', 'service', 'user']);

            if ($serviceId) {
                $query->where('service_id', $serviceId);
            }

            $products = $query->paginate($perPage, ['*'], 'page', $page);

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully',
                'data' => ProductResource::collection($products),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'pages_count' => $products->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $product = Product::with(['image', 'subCategory', 'sizes', 'service', 'user'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Product fetched successfully',
                'data' => new ProductResource($product)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    
    public function add_new_product(ProductActionRequest $request){
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([ 
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $data=$request->validated();
        // return $accessToken;
        $data['service_id']=$accessToken->tokenable_id;
        $service=Service::where('id',$accessToken->tokenable_id)->first();
        // return $service;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }
        $data['user_id']=$service->user_id;
        // return $data;
        $new_product=Product::create($data);
        if($new_product){
            // return $request;
            // return gettype($request->sizes);
            if(gettype($request->sizes)!='array'){
                $request->sizes=json_encode($request->sizes,true);
            }
            if(count($request->sizes)>0){
                $sizes=$request->sizes;
                // return $sizes;
                for($i=0;$i<count($sizes);$i++){
                    // return 'erer';
                    $new_size=ProductSize::create([
                        'size_id'=>$sizes[$i]->id,
                        'price'=>$sizes[$i]->price,
                        'product_id'=>$new_product->id,
                        ]);
                }
            }
            return response()->json(['status'=>true,'message'=>'Success To Add Product'],200);
        }
        return response()->json(['status'=>false,'message'=>'Faild To Add Product'],200);
    }
    public function update_one_product(ProductUpdateRequest $request){
        $id=request('id');
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $check_product=Product::where('id',$id)->first();
        if($check_product->service_id!=$accessToken->tokenable_id){
            return response()->json(['status'=>false,'message'=>'This Is Not For You'],203);
        }
        $data=$request->validated();
        
        $data['service_id']=$accessToken->tokenable_id;
        $service=Service::where('id',$accessToken->tokenable_id)->first();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }
        $data['user_id']=$service->user_id;
        unset($data['sizes']);
        $new_product=Product::where('id',$id)->update($data);
        if($new_product){
            $del_sizes=ProductSize::where('product_id',$id)->delete();
            
            if(count($request->sizes)>0){
                if(gettype($request->sizes)!='array'){
                    $request->sizes=json_decode($request->sizes,true);
                }
                $sizes=$request->sizes;
                for($i=0;$i<count($sizes);$i++){
                    $new_size=ProductSize::create([
                          'size_id'=>$sizes[$i]['id'],
                        'price'=>$sizes[$i]['price'],
                        'product_id'=>$id,
                        ]);
                }
            }
            return response()->json(['status'=>true,'message'=>'Success To Update Product'],200);
        }
        return response()->json(['status'=>false,'message'=>'Faild To Update Product'],200);
    }
    public function change_product_status(Request $request){
        $id=request('id');
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $check_product=Product::where('id',$id)->first();
        
        if($check_product->service_id!=$accessToken->tokenable_id){
            return response()->json(['status'=>false,'message'=>'This Is Not For You'],203);
        }
        
        $up=$check_product->update(['status'=>$check_product->status=='active'?'inactive':'active']);
        if($up){
            return response()->json(['status'=>true,'message'=>'Success To Change Status']);
        }
        return response()->json(['status'=>false,'message'=>'Faild To Change Status']);
    }
    public function delete_product(Request $request){
        $id=request('id');
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $check_product=Product::where('id',$id)->first();
        if($check_product->service_id!=$accessToken->tokenable_id){
            return response()->json(['status'=>false,'message'=>'This Is Not For You'],203);
        }
        $up=$check_product->delete();
        if($up){
        Offer::where('product_id',$id)->delete();
            return response()->json(['status'=>true,'message'=>'Success To Delete']);
        }
        return response()->json(['status'=>false,'message'=>'Faild To Delete']);
    }

    public function service_products(Request $request){
        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Enter token'],401);
        }
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        // $products=Product::with('productsize.size')->where('service_id',$accessToken->tokenable_id)->get();
        
        $products = Product::with('productsize.size')
    ->where('service_id', $accessToken->tokenable_id)
    ->get()
    ->transform(function ($product) {
        $offer = Offer::where('product_id', $product->id)
            ->where('end_date', '>', now())
            ->first();

        $product->is_in_offer = $offer ? true : false;

        if ($offer && is_numeric($offer->descounted_price) && $offer->descounted_price > 0) {
            $discountPercentage = floatval($offer->descounted_price) / 100;

            // Apply discount to product price (if it exists)
            if (!is_null($product->price)) {
                $product->price = round($product->price - ($product->price * $discountPercentage), 2);
            }

            // Apply discount to each product size price
            $product->productsize->transform(function ($size) use ($discountPercentage) {
                if (!is_null($size->price)) {
                    $size->price = round($size->price - ($size->price * $discountPercentage), 2);
                }
                return $size;
            });
        }

        return $product;
    });

        
        
        // return $products;
        return response()->json(['status'=>true,'message'=>'success','data'=>ServProductResource::collection($products)]);
    }
    public function prod_sizes(){
        $id=request('id');
        $prod_sizes=ProductSize::where('product_id',$id)->pluck('size_id')->toArray();
        $sizes=Size::whereIn('id',$prod_sizes)->get();
        $sizes=$sizes->map(function($size)use($id){
            $price=ProductSize::where('size_id',$size->id)->where('product_id',$id)->first();
            $size->price=$price->price;
            return $size;
        });
        return view('services.product_size',compact('sizes','id'));
    }
    
    public function assign_size(){
        $id=request('id');
        $prod_sizes=ProductSize::where('product_id',$id)->pluck('size_id')->toArray();
        $sizes=Size::whereNotIn('id',$prod_sizes)->get();
        return view('services.assign_size',compact('id','sizes'));
    }
    
    public function store_assigned_size(Request $request){
        $id=request('id');
        Product::where('id',$id)->update(['price'=>null]);
        $assign=ProductSize::create([
                'size_id'=>$request->size,
                'price'=>$request->price,
                'product_id'=>$id
            ]);
        return redirect()->route('prod_sizes',['id'=>$id])->with('success','تمت الاضافه بنجاح');
        // return $request;
        
    }
    public function unassigned_size(){
        $id=request('id');
        $prod_id=request('product_id');
        $del_unassign=ProductSize::where(['size_id'=>$id,'product_id'=>$prod_id])->delete();
        return response()->json([
            'status'=>'success',
            // ''
            ]);
        
    }
    public function update_size(){
        $size_id=request('size_id');
        $product_id=request('product_id');
        $productsize=ProductSize::where(['size_id'=>$size_id,'product_id'=>$product_id])->first();
        $size=Size::where('id',$size_id)->first();
        $size->price=$productsize->price;
        // $productsize->size=$size;
        // return $size;
        $sizes=Size::get();
        return view('services.update_size',compact('product_id','size','sizes'));
        // return $size_id;
        // $size=ProductSize::where(['product_id'=>$id,'size_id'])
    }
    public function store_update_size(Request $request){
        $size_id=request('size_id');
        $product_id=request('product_id');
        $up=ProductSize::where([
                'product_id'=>$product_id,
                'size_id'=>$size_id,
            ])->update([
                    'size_id'=>$request->size,
                    'price'=>$request->price,
                ]);
                if($up){
                    
        return redirect()->route('prod_sizes',['id'=>$product_id])->with('success','تم التعديل بنجاح');
                }
                
                return redirect()->route('product_sizes',['id'=>$product_id])->with('error','لم يتم التحديث');
        // return $request;
        // $
    }
    
    public function global_search(Request $request)
{
    // Validate the search_text input
    $validated = $request->validate([
        'search_text' => 'required|string',
    ]);

    $searchText = $validated['search_text'];

    // Search products with partial match on name (safe concatenation)
    $products = Product::with(['image', 'subCategory', 'sizes', 'service', 'user'])
        ->where('name', 'like', '%' . $searchText . '%')
        ->get();

    // Search services with active status and open status, ordered by featured flag
    $services = Service::where('status', 'active')
        ->where('name', 'like', '%' . $searchText . '%')
        ->where('open_status', 1)
        ->orderByRaw("FIELD(featured, 'featured', 'not_featured')")
        ->get();
    $data=new stdClass();
    $data->products=$products;
    $data->services=$services;
    // Return both results (you can adjust as needed for your frontend)
    return response()->json([
        'success' => true,
        'message' => '',
        'data'=>$data
    ]);
}

}
