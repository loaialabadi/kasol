<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Offer;
use App\Models\Service;
use App\Models\User;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\Branch;
use App\Models\ProductSize;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Size;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\ProductExcelRequest;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Imports\ProductImport;
use Laravel\Sanctum\PersonalAccessToken;
use stdClass;
class ServiceController extends Controller
{
    public function index(Request $request)
    {
        // $user=auth()->id();
        $category_id=request()->query('category_id');
        $sub_category_id=request()->query('sub_category');
        // return $user;
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



            $services = Service::with([
                'products.productsize.size',
                'adds',
                'offers',
                'images',
                'branches',
                'stories',
                'user.stories' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'user'
            ])->when($category_id,function($query)use($category_id){
                $query->where('category_id',$category_id);
            })
            ->where('status','active')
            ->where('open_status',1)
            ->when($sub_category_id, function($query) use ($sub_category_id) {
                // dd($sub_category_id);
    $query->whereHas('products', function($query) use ($sub_category_id) {
        $query->where('sub_category_id', $sub_category_id);
    });
    
})
// ->orderBy('id', 'asc')
->orderByRaw("FIELD(featured, 'featured', 'not_featured')") // ترتيب المميزة أولًا

            ->paginate($perPage, ['*'], 'page', $page);
            
$services->getCollection()->transform(function ($service) {
    
    foreach ($service->offers as $offer) {
        $product = $offer->product;

        if ($product && is_numeric($offer->descounted_price) && $offer->descounted_price > 0) {
            $discountPercentage = floatval($offer->descounted_price) / 100;

            // **تحديث سعر العرض ليكون السعر بعد الخصم**
            if (!is_null($product->price)) {
                $offer->price = round($product->price - ($product->price * $discountPercentage), 2);
                $offer->save(); // حفظ السعر الجديد في قاعدة البيانات
            }

            // **تطبيق الخصم على كل حجم داخل `productsize`**
            foreach ($product->productsize as $size) {
                if (!is_null($size->price)) {
                    $size_data=Size::where('id',$size->size_id)->first();
                    $size->name=isset($size_data)?$size_data->name:'';
                    $size->size=$size_data;
                    $size->price = round($size->price - ($size->price * $discountPercentage), 2);
                }
            }
        }
    }
    
    $service->products->transform(function ($product) {
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

    return $service;
});




            if ($services->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No services found',
                    'data'=>[]
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'services fetched successfully',
                'data' => ServiceResource::collection($services),
                'pagination' => [
                    'total' => $services->total(),
                    'per_page' => $services->perPage(),
                    'current_page' => $services->currentPage(),
                    'pages_count' => $services->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $service = Service::with(['products', 'offers', 'images', 'branches', 'user.stories','adds', 'stories', 'user'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'service fetched successfully',
                'data' => new ServiceResource($service)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'service not found'
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching service',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show_service($id)
    {
        try {
            $service = Service::with(['images', 'branches', 'user.stories', 'stories', 'user'])->findOrFail($id);
            // return $service;
            return view('services.show_service',compact('service'));
            // return response()->json([
            //     'success' => true,
            //     'message' => 'service fetched successfully',
            //     'data' => new ServiceResource($service)
            // ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'service not found'
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching service',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function services_page(){
        $services=Service::paginate(10);
        // return $categories;
        return view('services.services_page',compact('services'));
    }
    public function add_new_service(){
        $users=User::get();
        // return $users;
        $categories=Category::where('status','active')->get();
        return view('services.add_new_service',compact('users','categories'));
    }
    public function store_new_service(Request $request){
        // return $request;
        $validator = Validator::make($request->all(), [
            'user' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'password'=>'required',
            'service_ratio'=>'required',
            'email' => 'required|email|max:255|unique:services,email',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:services,phone',
            'banner' => 'nullable|image',
            'logo' => 'nullable|image',
            'menu' => 'nullable|file',
            'baner' => 'nullable|file',
            'feature' => 'required|in:featured,not_featured',
            'description' => 'nullable|string',
             'start_work_date' => 'required|date_format:H:i',
        'end_work_date' => 'required|date_format:H:i',
            // 'start_work_date' => 'required|date_format:H:i',
            // 'end_work_date' => 'required|date_format:H:i|after:start_work_date',
        ]);
        
        
          $startWorkTime = '05:00';
    $endWorkTime = '23:59';

    // if ($request->start_work_date !== $startWorkTime) {
    //     return redirect()->back()->withErrors(['start_work_date' => 'يجب أن يكون وقت الفتح '.$startWorkTime])->withInput();
    // }
    // التحقق: إذا كان وقت الإغلاق أكبر من المحدد، يرجع خطأ
    // if (strtotime($request->end_work_date) > strtotime($endWorkTime)) {
    //     return redirect()->back()->withErrors(['end_work_date' => 'يجب ألا يكون وقت الإغلاق بعد '.$endWorkTime])->withInput();
    // }

       $bannerPath = $request->hasFile('banner') ? $request->file('banner')->store('service', 'public') : null;
$logoPath   = $request->hasFile('logo') ? $request->file('logo')->store('service', 'public') : null;
$menuPath   = $request->hasFile('menu') ? $request->file('menu')->store('service', 'public') : null;

        // $banerPath = $request->hasFile('baner') ? $request->file('baner')->store('baners', 'public') : null;

        if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();

            // return redirect()->back()->with('errors','Enter All Data');
            // return response()->json(['errors' => $validator->errors()], 422);
        }

        $new_one=Service::create([
            'user_id' => $request->user,
            'name' => $request->name,
            'email' => $request->email,
            'password'=>Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'service_ratio' => $request->service_ratio,
            'logo' => $logoPath,
            'menu' => $menuPath,
            'feature' => $request->feature,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'start_work_date' => $request->start_work_date,
            'end_work_date' => $request->end_work_date,
            'baner' => $bannerPath,
        ]);


        if($new_one){
            return redirect()->route('services_page')->with('success', 'تمت الاضافه');
        }
        return redirect()->route('services_page',['id'=>$request->id])->with('faild','لم تتم الاضافه');
    }
    public function store_update_service(Request $request, $id){
        // return $request;
        $id=request('id');
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'service_ratio' => 'required',
            'address' => 'required',
            'password'=>'nullable',
            'phone' => 'required',
            'banner' => 'nullable',
            'logo' => 'nullable',
            'menu' => 'nullable',
            'feature' => 'required|in:featured,not_featured',
            'description' => 'required|string',
            'start_work_date' => 'required',
            'end_work_date' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors','أدخل كل البيانات');
            // return response()->json(['errors' => $validator->errors()], 422);
        }
        $data=[];
        $data['user_id']=$request->user_id;
        $data['name']=$request->name;
        $data['email']=$request->email;
        $data['service_ratio']=$request->service_ratio;
        $data['address']=$request->address;
        $data['phone']=$request->phone;
        $data['featured']=$request->feature;
        $data['description']=$request->description;
        $data['start_work_date']=$request->start_work_date;
        $data['end_work_date']=$request->end_work_date;
        if($request->password!=null){
           $data['password']=Hash::make($request->password);
        }
        if($request->hasFile('banner')){
            $bannerPath = $request->hasFile('banner') ? $request->file('banner')->store('banners', 'public') : null;
            $data['baner']=$bannerPath;
        }
        if($request->hasFile('logo')){
            $logoPath = $request->hasFile('logo') ? $request->file('logo')->store('logos', 'public') : null;
            $data['logo']=$logoPath;
        }
        if($request->hasFile('menu')){
            $menuPath = $request->hasFile('menu') ? $request->file('menu')->store('menus', 'public') : null;
            $data['menu']=$menuPath;
        }
        // $sub=Service::where('id',$id)->first();
        // $data['category_id']=$request->id;
        $new_one=Service::where('id',$id)->update($data);
        if($new_one){
            return redirect()->route('services_page')->with('success','تم التعديل');
        }
        return redirect()->route('services_page')->with('faild','لم يتم التعديل');
    }
    public function update_service(){
        $id=request('id');
        $users=User::get();
        $service=Service::where('id',$id)->first();
        $categories=Category::where('status','active')->get();
        return view('services.update_service',compact('id','service','users','categories'));
    }
    public function change_service_status(){
        $id = request('id');
        $category = Service::find($id); // It's more efficient to use find() here instead of where()->first()

        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $up = $category->update(['status' => $category->status == 'active' ? 'inactive' : 'active']);

            if ($up) {
                return redirect()->back()->with('success', 'تم تغيير الحاله');
            }

            return redirect()->back()->with('error', 'لم يتم التغيير'); // Fixed typo here
        }

        return redirect()->back()->withErrors(['لم يتواجد']);
    }
    public function delete_services(){
        $id = request('id');
        $category = Service::find($id); // It's more efficient to use find() here instead of where()->first()
       
        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $del = $category->delete();

            if ($del) {
                return redirect()->back()->with('success', 'تم الحذف ');
            }

            return redirect()->back()->with('error', 'لم يتم الحذف '); // Fixed typo here
        }

        return redirect()->back()->withErrors(['لم يتواجد']);
    }


    public function search_services(Request $request)
    {
        $searchQuery = $request->get('search');

        // Fetch categories based on the search query
        $categories = Service::where('name', 'LIKE', '%' . $searchQuery . '%')->paginate(10);

        // Return the results to the view
        return view('categories.categories_page', compact('categories'));
    }
    public function service_offers(){
        $id=request('id');
        $offers=Offer::where('service_id',$id)->paginate(10);
        $offers->map(function ($offer) {
    $offer->final_price = $offer->price - (($offer->descounted_price * $offer->price) / 100);
    return $offer;
});
// return $offers;
        return view('services.service_offers',compact('offers','id'));
    }



    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'category_id'=>'required',
                'baner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'menu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'rating' => 'nullable|numeric|min:0|max:5',
                'status' => 'nullable|in:active,inactive',
                'description' => 'required|string',
                'start_work_date' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
                'end_work_date' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', 'after_or_equal:start_work_date'],
                'price_range' => 'nullable|string|max:255',
                'image_id' => 'nullable|integer|exists:images,id',
                'password'=>'required',
            ]);

            if ($request->hasFile('baner')) {
                $validatedData['baner'] = $request->file('baner')->store('services/banner', 'public');
            }
            if ($request->hasFile('logo')) {
                $validatedData['logo'] = $request->file('logo')->store('services/logo', 'public');
            }
            if ($request->hasFile('menu')) {
                $validatedData['menu'] = $request->file('menu')->store('services/menu', 'public');
            }
            if($request->password){
                $validatedData['password']=Hash::make($request->password);
            }

            $service = Service::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => new ServiceResource($service),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while creating the service',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'address' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'email' => 'sometimes|email|max:255',
                'baner' => 'sometimes|string|max:255',
                'logo' => 'sometimes|string|max:255',
                'menu' => 'sometimes|string|max:255',
                'rating' => 'sometimes|numeric|min:0|max:5',
                'status' => 'sometimes|in:active,inactive',
                'description' => 'sometimes|string|max:500',
                'start_work_date' => ['sometimes', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
                'end_work_date' => ['sometimes', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', 'after_or_equal:start_work_date'],
                'price_range' => 'sometimes|string|max:50',
                'image_id' => 'sometimes|integer|exists:images,id',
            ]);

            $service = Service::findOrFail($id);
            $service->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function service_subcats(Request $request){
         if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Token Nedded'],203);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        // return $accessToken;
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $service=Service::where('id',$accessToken->tokenable_id)->first();
        // return $service;
        $subcats=Subcategory::where('category_id',$service->category_id)->get();
        return response()->json(['status'=>true,'message'=>'success','data'=>$subcats]);
        // return $service;
        // $user=User::where('id',$service->user_id)->first();
        // return $user;
    }
    
    public function service_profile(Request $request){
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
        // return $accessToken->tokenable_id;
        $service=Service::where('id',$accessToken->tokenable_id)->first([
            'name',
        'address',
        'phone',
        'email',
        'baner',
        'logo',
        'menu',
        'rating',
        'status',
        'description',
        'start_work_date',
        'end_work_date',
        'price_range',
        'image_id',
        'category_id',
        'open_status'
        
            ]);
        return response()->json(['status'=>true,'message'=>'Success To Get Data','data'=>$service]);
    }
    
    
    
    public function service_update_profile(Request $request){
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
           $rules = [
        'name'             => 'nullable',
        'address'          => 'nullable',
        'phone'            => 'nullable',
        // 'email'            => 'nullable',
        'baner'            => 'nullable',
        'logo'             => 'nullable',
        'menu'             => 'nullable',
        'rating'           => 'nullable',
        'status'           => 'nullable',
        'description'      => 'nullable',
        'start_work_date'  => 'nullable',
        'end_work_date'    => 'nullable',
        'price_range'      => 'nullable',
        'category_id'      => 'nullable',
    ];

    // Validate request data
    $validator = Validator::make($request->all(), $rules);
// return $validator;
$validatedData = $validator->validated();
// return $validatedData;
    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'errors'  => $validator->errors(),
        ], 422);
    }
    
    
      $service = Service::find($accessToken->tokenable_id);
    
    if (!$service) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Service not found!',
        ], 404);
    }
    
      if ($request->hasFile('baner')) {
        // Delete old file if exists
        if ($service->baner) {
            Storage::delete($service->baner);
        }
         $imagePath = $request->hasFile('baner') ? $request->file('baner')->store('service', 'public') : null;

                $validatedData['baner']=$imagePath;
        // $validatedData['banner']=$request->file('baner')->store('banners');
        // $service->baner = $request->file('baner')->store('banners');
    }

    if ($request->hasFile('logo')) {
        if ($service->logo) {
            Storage::delete($service->logo);
        }
        
                    $imagePath = $request->hasFile('logo') ? $request->file('logo')->store('service', 'public') : null;

                $validatedData['logo']=$imagePath;

        // $service->logo = $request->file('logo')->store('logos');
    }

    if ($request->hasFile('menu')) {
        if ($service->menu) {
            Storage::delete($service->menu);
        }
           $imagePath = $request->hasFile('menu') ? $request->file('menu')->store('service', 'public') : null;

                $validatedData['menu']=$imagePath;
                // $validatedData['menu']=$request->file('menu')->store('menu');

        // $service->menu = $request->file('menu')->store('menus');
    }

    // Update service fields
    $service->update($validatedData);
return response()->json(['status'=>true,'message'=>'Success To Update','data'=>$service],200);
    
    
    
    }
    public function service_products(){
        $id=request('id');
        $products=Product::with('subCategory')->where('service_id',$id)->paginate(10);
        $service=Service::where('id',$id)->first();
        $subcats=Subcategory::where('category_id',$service->category_id)->get();
        
        // return $products[0];
        return view('services.service_products',compact('products','id','subcats'));
        return $id;
    }
    public function add_new_ser_prod(){
        $id=request('id');
        $service=Service::where('id',$id)->first();
        $subcats=Subcategory::where('category_id',$service->category_id)->get();
        return view('services.add_service_product',compact('subcats','id'));
        // return $subcats;
    }
    public function store_serv_prod(Request $request){
        $id=request('id');
        $service=Service::where('id',$id)->first();
        // return $service;
        $rules = [
        'sub_category_id'             => 'required',
        'name'             => 'required',
        'slug'             => 'required',
        'description'             => 'required',
        'price'             => 'required|number',
        'image'             => 'required',
    ];

    // Validate request data
    $validator = Validator::make($request->all(), $rules);
// return $validator;
$validatedData = $validator->validated();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }
        $validatedData['service_id']=$service->id;
        $validatedData['user_id']=$service->user_id;
        $validatedData['category_id']=$service->category_id;
        // return $validatedData;
        $new=Product::create($validatedData);
        if($new){
            return redirect()->route('service_products',['id'=>$id])->with('success','تمت الاضافه بنجاح');
        }
        return redirect()->route('service_products')->with('error','لم تتم الاضافه');
    }
    
    public function delete_product(){
        $id=request('id');
        $product=Product::where('id',$id)->first();
        $serv=$product->service_id;
        $product->delete();
        return redirect()->back()->with('success','تم المسح بنجاح');
        // return $product;
    }
    
     public function update_product(){
        $id=request('id');
       
        $product=Product::where('id',$id)->first();
        $service=Service::where('id',$product->service_id)->first();
        // return $service;
        $subcats=Subcategory::where('category_id',$service->category_id)->get();
        return view('services.update_service_product',compact('subcats','product','id'));
        // return $subcats;
    }   
    public function update_serv_prod(Request $request){
         $id=request('id');
        $service=Service::where('id',$id)->first();
        // return $service;
        $rules = [
        'sub_category_id'             => 'required',
        'name'             => 'required',
        'slug'             => 'required',
        'description'             => 'required',
        'price'             => 'nullable',
        'image'             => 'nullable',
    ];


    // Validate request data
    $validator = Validator::make($request->all(), $rules);
// return $validator;
$validatedData = $validator->validated();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }
        
         $check_sizes=ProductSize::where('product_id',$id)->first();
        // return $check_sizes;
        if($check_sizes!=null){
            $validatedData['price']=null;
        }
        
        // $validatedData['service_id']=$service->id;
        // $validatedData['user_id']=$service->user_id;
        // $validatedData['category_id']=$service->category_id;
        // return $validatedData;
        $prod=Product::where('id',$id)->first();
        $new=$prod->update($validatedData);
        if($new){
            return redirect()->route('service_products',['id'=>$prod->service_id])->with('success','تم التعديل بنجاح');
        }
        return redirect()->back()->with('error','حدث خطأ ما');
        
    }
    public function upload_excel_products(Request $request){
        
        $id=request('id');
        $service=Service::where('id',$id)->first();
        
        // return $request;
          $request->validate([
            'file' => 'required'
        ]);

        // Get the uploaded file
        $file = $request->file('file');
// return $file;
        // Load the Excel file
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $data = [];
        $highestRow = $worksheet->getHighestRow(); // Get the last row number
        $highestColumn = $worksheet->getHighestColumn(); // Get the last column letter
// return $request;
        // Loop through each row (starting from row 2 to skip headers)
       for ($row = 2; $row <= $highestRow; $row++) {
    $data = [
        'sub_category_id' => $request->subcategory_id, // Get from request
        'name' => $worksheet->getCell("C$row")->getValue(),
        'slug' => $worksheet->getCell("C$row")->getValue(), // Slug can be generated dynamically if needed
        'description' => $worksheet->getCell("D$row")->getValue(),
        'price' => (double)$worksheet->getCell("E$row")->getValue(),
        'image' => $worksheet->getCell("A$row")->getValue()??null,
        'user_id'=>$service->user_id,
        'service_id' => $id, // Ensure `$id` is defined before the loop
    ];
// return $data;
    // Insert into the database
    Product::create($data);
}

        
        return redirect()->back()->with('success','تم الرفع بنجاح');

        
        
        
        // return $request;
        // try {
        //     Excel::import(new ProductImport, $request->file('file'));
        //     return redirect()->back()->with('success','تم الرفع بنجاح');
        //     // return response()->json([
        //     //     'message' => 'تم رفع البيانات بنجاح',
        //     // ], 200);
        // } catch (\Exception $e) {
        //     return $e;
        //     // return response()->json([
        //     //     'error' => 'Failed to import projects: ' . $e->getMessage(),
        //     // ], 500);
        // }
    }
    public function update_serv_deliv(Request $request){
        $id=request('id');
        $serv=Service::where('id',$id)->first();
        $chan=$serv->update(['has_delivery'=>$serv->has_delivery==1?0:1]);
        return redirect()->back()->with('success','تم التعديل بنجاح');
    }
    
    public function change_open_status(Request $request){
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
        $serv=Service::where('id',$accessToken->tokenable_id)->first();
        $change=$serv->update(['open_status'=>$serv->open_status==1?0:1]);
        if($change){
            return response()->json([
                    'status'=>true,
                    'message'=>!$serv->open_status==1?'تم الغلق بنجاح':'تم الفتح بنجاح',
                ],200);
        }
        
    }
    public function service_branch(){
        $id=request('id');
        $branches=Branch::where('service_id',$id)->paginate(10);
        // return $branches;
        return view('services.service_branch',compact('branches','id'));
    }
    public function add_new_branch(){
        $id=request('id');
        return view('services.add_new_branch',compact('id'));
        // return $id;
    }
    public function store_new_branch(Request $request){
        $id=request('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_name' => 'required|unique:branches,user_name',
            'password' => 'required',
            'image' => 'required',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:services,phone',
            'start_work_date' => 'required|date_format:H:i',
            'end_work_date' => 'required|date_format:H:i',
        ]);
         if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();

            // return redirect()->back()->with('errors','Enter All Data');
            // return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $img_path = $request->hasFile('image') ? $request->file('image')->store('banners', 'public') : null;
        $validatedData = $validator->validated();
        $validatedData['image']=$img_path;
        $validatedData['password']=Hash::make($request->password);
        $validatedData['service_id']=$id;
        $new=Branch::create($validatedData);
        // return $new;
        if($new){
            return redirect()->route('service_branch',['id'=>$id])->with('success','تمت الاضافه بنجاح');
        }
        return redirect()->route('service_branch',['id'->$id])->with('error','لم تتم الاضافه');
        // return $validatedData;
        // return $request;
    }
    public function delete_branch(){
        $id=request('id');
        $del=Branch::where('id',$id)->delete();
        return redirect()->back()->with('success','تم الحذف بنجاح');
    }
    public function update_branch(){
        $id=request('id');
        $branch=Branch::where('id',$id)->first();
        return view('services.update_branch',compact('id','branch'));
    }
    
    
    public function store_update_branch(Request $request){
        $id=request('id');
        // return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_name' => 'required|unique:branches,user_name,'.$id,
            'password' => 'nullable',
            'image' => 'nullable',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:services,phone',
            'start_work_date' => 'required',
            'end_work_date' => 'required',
        ]);
         if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();

            // return redirect()->back()->with('errors','Enter All Data');
            // return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $validatedData = $validator->validated();
        if($request->hasFile('image')){
            $img_path =$request->file('image')->store('banners', 'public');
            $validatedData['image']=$img_path;
        }
        // $img_path = $request->hasFile('image') ? $request->file('image')->store('banners', 'public') : null;
        // $validatedData['image']=$img_path;
        if($request->password!=null){
            $validatedData['password']=Hash::make($request->password);
        }
        else{
            unset($validatedData['password']);
        }
        // $validatedData['service_id']=$id;
        $bran=Branch::where('id',$id)->first();
        $up=$bran->update($validatedData);
        // return $new;
        if($up){
            return redirect()->route('service_branch',['id'=>$bran->service_id])->with('success','تمت الاضافه بنجاح');
        }
        return redirect()->route('service_branch',['id'->$bran->service_id])->with('error','لم تتم الاضافه');
        // return $validatedData;
        // return $request;
    }
    
    public function service_statistics(Request $request){
         if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Token Nedded'],203);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        // return $accessToken;
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                    ],401);
        }
        $service_id=$accessToken->tokenable_id;
        
        $from_date = request()->query('from_date');
        $to_date = request()->query('to_date');
    
        // Logic for handling date defaults
        if (!$from_date && !$to_date) {
            // No dates provided -> Use today
            $from_date = $to_date = Carbon::today()->toDateString();
        } elseif ($from_date && !$to_date) {
            // Only from_date provided -> Use from_date to today
            $to_date = Carbon::today()->toDateString();
        }
        
        
        $orders_number=ServiceOrder::where('service_id',$service_id)->whereBetween('date', [$from_date , $to_date])->count();
        $cash_count=ServiceOrder::where('service_id',$service_id)->where('pay_method','cash')->whereBetween('date', [$from_date , $to_date])->count();
        $online_count=ServiceOrder::where('service_id',$service_id)->where('pay_method','online')->whereBetween('date', [$from_date , $to_date])->count();
        $total_cash=ServiceOrder::where('service_id',$service_id)->where('pay_method','cash')->whereBetween('date', [$from_date , $to_date])->sum('order_value');
        $total_online=ServiceOrder::where('service_id',$service_id)->where('pay_method','online')->whereBetween('date', [$from_date , $to_date])->sum('order_value');
        $service=Service::where('id',$service_id)->first();
        $service_money=$service->money;
        $total_discount=ServiceOrder::where('service_id',$service_id)->sum('discount_price');
        $data=new stdClass();
        $data->orders_number=$orders_number;
        $data->cash_count=$cash_count;
        $data->total_discount=$total_discount;
        $data->online_count=$online_count;
        $data->total_cash=$total_cash;
        $data->total_online=$total_online;
        $data->service_money=$service_money;
        return response()->json([
                'status'=>true,
                'data'=>$data,
                'message'=>''
            ]);
    }
    public function web_service_statistics(Request $request){
        $service_id=request('id');
        $orders_number=ServiceOrder::where('service_id',$service_id)->count();
        $cash_count=ServiceOrder::where('service_id',$service_id)->where('pay_method','cash')->count();
        $online_count=ServiceOrder::where('service_id',$service_id)->where('pay_method','online')->count();
        $total_cash=ServiceOrder::where('service_id',$service_id)->where('pay_method','cash')->sum('order_value');
        $total_discount=ServiceOrder::where('service_id',$service_id)->sum('discount_price');
        $total_online=ServiceOrder::where('service_id',$service_id)->where('pay_method','online')->sum('order_value');
        $service=Service::where('id',$service_id)->first();
        $service_money=$service->money;
        
        $data=new stdClass();
        $data->orders_number=$orders_number;
        $data->cash_count=$cash_count;
        $data->online_count=$online_count;
        $data->discount_price=$total_discount;
        $data->total_cash=$total_cash;
        $data->total_online=$total_online;
        $data->service_money=$service_money;
        $id=$service_id;
        return view('services.service_statistics',compact('data','id'));
        // return response()->json([
        //         'status'=>true,
        //         'data'=>$data
        //     ]);
    }
    public function money_trans(Request $request){
        $id=request('id');
        $validator = Validator::make($request->all(), [
            'type'=>'required',
            'mon_val'=>'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors','أدخل كل البيانات');
            // return response()->json(['errors' => $validator->errors()], 422);
        }
        $service=Service::where('id',$id)->first();
        if($request->type=='give'){
            $up=$service->update(['money'=>$service->money-(float)$request->mon_val]);
            if($up){
                return redirect()->back()->with('success','تم الاعطاء بنجاح');
            }
            return redirect()->back()->with('error','لم يتم الاعطاء حاول من جديد');
        }
        else{
            $up=$service->update(['money'=>$service->money+(float)$request->mon_val]);
            if($up){
                return redirect()->back()->with('success','تم الاخذ بنجاح');
            }
            return redirect()->back()->with('error','لم يتم الاخذ حاول من جديد');
        }
        // return $request;
    }
    
}