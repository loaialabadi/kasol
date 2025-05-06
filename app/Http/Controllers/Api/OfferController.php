<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Images;
use StdClass;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\OffersResource;
use Illuminate\Support\Facades\Validator;

use Laravel\Sanctum\PersonalAccessToken;

class OfferController extends Controller
{
    /**
     * Display a listing of offers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
            $currentDate = date('Y-m-d');
            $offers = Offer::with([ 'user', 'image','service','product.productsize.size'])
            ->whereDate('end_date', '>=', $currentDate)
                ->paginate($perPage, ['*'], 'page', $page);//'service.products','service.adds','service.images','service.offers',

            if ($offers->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No offers found',
                    'data'=>[]
                ], 200);
            }
            
            $offers->map(function ($offer) {
    $offer->price = $offer->price - (($offer->descounted_price * $offer->price) / 100);
    return $offer;
});

$offers->map(function ($offer) {
    if ($offer->product && isset($offer->product['productsize'])) {
        foreach ($offer->product['productsize'] as &$size) {
            if (!empty($offer->descounted_price) && $offer->descounted_price > 0) {
                // تطبيق الخصم على سعر الحجم
                $size['price'] = $size['price'] - (($offer->descounted_price * $size['price']) / 100);
            }
        }
    }

    // ضبط السعر الرئيسي للعرض إذا لم يكن مرتبطًا بحجم
    if (!empty($offer->descounted_price) && $offer->descounted_price > 0) {
        $offer->price = $offer->product->price - (($offer->descounted_price / 100) * $offer->product->price);
    }

    return $offer;
});


            return response()->json([
                'success' => true,
                'message'=> 'Offers fetched successfully',
                'data' => OfferResource::collection($offers),
                'pagination' => [
                    'total' => $offers->total(),
                    'per_page' => $offers->perPage(),
                    'current_page' => $offers->currentPage(),
                    // 'last_page' => $offers->lastPage(),
                    'pages_count' => $offers->lastPage(),  
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching offers',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified offer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
     

     
    public function show($id)
    {
        try {
            $offer = Offer::with(['service', 'user', 'image'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'massage' => 'Offers fetched successfully',
                'data' => new OfferResource($offer)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the offer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function search_offer(Request $request)
    {
        $searchQuery = $request->get('search');
        $id=request('id');
        // return $id;
        // Fetch subcategories based on the search query
    //  return $id;
            $currentDate = date('Y-m-d');
    $offers = Offer::with('product')
    ->where('service_id', $id)
    ->whereDate('end_date', '>=', $currentDate)
    ->where(function ($query) use ($searchQuery) {
        $query->where('title', 'LIKE', '%' . $searchQuery . '%')
              ->orWhere('description', 'LIKE', '%' . $searchQuery . '%')
              ->orWhere('descounted_price', $searchQuery);
    })
    ->paginate(10);
// return $offers;
        // Return the results to the view
        return view('services.service_offers', compact('offers','id'));
    }


    public function add_new_offer(){
        $id=request('id');
        $products=Product::where('service_id',$id)->get();
        return view('offers.add_new_offer',compact('products','id'));
    }
    public function store_new_offer(Request $request){
        // return $request;
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'images' => 'nullable',
            'discount_rate' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
            'price' => 'nullable',
            'descounted_price' => 'required',
            'stars_rate' => 'nullable',
            'service_id' => 'nullable',
            'user_id' => 'nullable',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user=Auth::guard('admin')->user();
        // return $request;
        $data=[];
        $product=Product::where('id',$request->product_id)->first();
        $service=Service::where('id',$request->id)->first();
        $data['title']=$request->title;
        $data['description']=$request->description;
       if ((float)$product->price > 0) {
    $data['discount_rate'] = ((float)$product->price - (float)$request->descounted_price) / (float)$product->price * 100;
} else {
    $data['discount_rate'] = 0; // Set to 0 or handle accordingly
}

        $data['start_date']=$request->start_date;
        $data['end_date']=$request->end_date;
        $data['created_by']=$user->name." (admin($user->id))";
        $data['price']=$product->price;
        $data['product_id']=$product->id;
        $data['descounted_price']=$request->descounted_price;
        $data['user_id']=$service->user_id;
        $data['service_id']=$request->id;
        $new_offer=Offer::create($data);
        // return $new_offer;
        if ($new_offer) {
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    // Generate a unique file name (optional, as `store()` does this automatically)
                    $imageName = time() . '_' . $img->getClientOriginalName();

                    // Store the image in the 'offer_images' directory in public disk
                    $imagePath = $img->storeAs('offer_images', $imageName, 'public');

                    // Save image data to the database (assuming an OfferImage model)
                    Images::create([
                        'offer_id' => $new_offer->id, // Link to offer
                        'image' => $imagePath, // Store path
                    ]);
                }
            }
            return redirect()->route('service_offers',['id'=>$request->id])->with('success','تمت الاضافه');
        }
        return redirect()->route('service_offers',['id'=>$request->id])->with('error','لم تتم الاضافه');
        // return $data;
    }

    public function store_update_offer(Request $request){
        $id=$request->id;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'images' => 'nullable|array',
            'images.*' => 'mimes:jpg,jpeg,png,gif|max:2048',
            'descounted_price' => 'required|numeric',
        ]);

        $offer = Offer::findOrFail($request->id); // Find the offer by ID

        // Update the offer details
        $up=$offer->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'product_id' => $request->product_id,
            'descounted_price' => $request->descounted_price,
        ]);

        if($up){
            // Handle the uploaded images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    // Generate a unique file name (optional, as `store()` does this automatically)
                    $imageName = time() . '_' . $img->getClientOriginalName();

                    // Store the image in the 'offer_images' directory in public disk
                    $imagePath = $img->storeAs('offer_images', $imageName, 'public');

                    // Save image data to the database (assuming an OfferImage model)
                    Images::create([
                        'offer_id' => $id, // Link to offer
                        'image' => $imagePath, // Store path
                    ]);
                }
            }
            return redirect()->route('service_offers',['id'=>$offer->service_id])->with('success','تم التحديث');
        }
        return redirect()->route('service_offers',['id'=>$offer->service_id])->with('error','لم يتم التحديث');

        // Handle image deletions
        // if ($request->has('delete_images')) {
        //     foreach ($request->delete_images as $imageId) {
        //         $image = Images::findOrFail($imageId);
        //         // Delete the image file from storage
        //         Storage::delete($image->path);
        //         // Delete the record from the database
        //         $image->delete();
        //     }
        // }
    }

    public function update_offer(){
        $id=request('id');
        $offer=Offer::with('images')->where('id',$id)->first();
        $products=Product::where('service_id',$offer->service_id)->get();
        return view('offers.update_offer',compact('offer','products','id'));
    }

    public function deleteImage($imageId)
      {
          $image = Images::find($imageId);
          if ($image) {
              // Delete the image file from storage
              // Storage::delete($image->path);

              // Delete the record from the database
              $image->delete();
          }

          return back()->with('success', 'Image deleted successfully');
      }
          public function get_store_offers(Request $request){
        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Send Auth']);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                        'data'=>[],
                    ],401);
        }
                $currentDate = date('Y-m-d');
        // return $accessToken->tokenable_id;
        $offers=Offer::with('product')->whereDate('end_date', '>=', $currentDate)->where('service_id',$accessToken->tokenable_id)->get();
        return response()->json(['status'=>true,'message'=>'','data'=>OffersResource::collection($offers)],200);
      }
      
       public function service_add_new_offer(Request $request){
        if(!$request->header('Authorization')){
            return response()->json(['status'=>false,'message'=>'Send Auth']);
        }
        $token = explode(' ', $request->header('Authorization'))[1];

        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
                return response()->json([
                        'success' => false,
                        'message' => 'Session Ended Login Again',
                        'data'=>[],
                    ],401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'description' => 'nullable',
            'images' => 'nullable',
            'discount_rate' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
            'price' => 'nullable',
            'descounted_price' => 'required',
            'stars_rate' => 'nullable',
            'service_id' => 'nullable',
            'user_id' => 'nullable',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); 
        }
        // $user=Auth::guard('admin')->user();
        // return $user;
        $service=Service::where('id',$accessToken->tokenable_id)->first();
        $data=[];
        $product=Product::where('id',$request->product_id)->first();
        // $service=Service::where('id',$request->id)->first();
        $data['title']=$request->title;
        $data['description']=$request->description;
       $data['discount_rate'] = ($product->price > 0)  
    ? (($product->price - $request->discounted_price) / $product->price) * 100  
    : 0;
        
        $data['start_date']=$request->start_date;
        $data['end_date']=$request->end_date;
        $data['created_by']='the Store';
        $data['price']=$product->price;
        $data['product_id']=$product->id;
        $data['descounted_price']=$request->descounted_price;
        $data['user_id']=$accessToken->tokenable_id;
        $data['service_id']=$accessToken->tokenable_id;
        $data['user_id']=$service->user_id;
        // return $data;
        $new_offer=Offer::create($data);
        
        // return $new_offer;
        if ($new_offer) {
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    // Generate a unique file name (optional, as `store()` does this automatically)
                    $imageName = time() . '_' . $img->getClientOriginalName();

                    // Store the image in the 'offer_images' directory in public disk
                    $imagePath = $img->storeAs('offer_images', $imageName, 'public');

                    // Save image data to the database (assuming an OfferImage model)
                    Images::create([
                        'offer_id' => $new_offer->id, // Link to offer
                        'image' => $imagePath, // Store path
                    ]);
                }
            }
            return response()->json(['status'=>true,'message'=>'success To Add'],200);
        }
        return response()->json(['status'=>false,'message'=>'Fails To Add'],203);

    }
    
    
    
        public function service_update_offer(Request $request)
    {
        $id=request('id');
        // Check for Authorization header
        if (!$request->header('Authorization')) {
            return response()->json([
                'status' => false,
                'message' => 'Send Auth'
            ], 401);
        }

        // Extract and validate token
        $token = explode(' ', $request->header('Authorization'))[1] ?? null;
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Token',
                'data' => []
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if ($accessToken === null) {
            return response()->json([
                'success' => false,
                'message' => 'Session Ended, Login Again',
                'data' => []
            ], 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|exists:offers,id', // Ensure ID is provided and exists in DB
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate images
            'discount_rate' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'price' => 'nullable|numeric',
            'descounted_price' => 'nullable|numeric',
            'stars_rate' => 'nullable|numeric|min:0|max:5',
            'service_id' => 'nullable|integer|exists:services,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'product_id' => 'required|integer|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Extract validated data
        $data = $validator->validated();
        // $id = $data['id']; // Get the ID from validated data
        // unset($data['id']); // Remove ID from update data

        // Update offer
        $update_offer = Offer::where('id', $id)->update($data);

        if ($update_offer) {
            // Handle images if uploaded
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $imageName = time() . '_' . $img->getClientOriginalName();
                    $imagePath = $img->storeAs('offer_images', $imageName, 'public');

                    Images::create([
                        'offer_id' => $id, // Use $id since the offer is already updated
                        'image' => $imagePath
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Offer Updated Successfully'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to Update Offer'
        ], 400);
    }
    
        public function service_delete_offer(){
        $id = request('id');
        $offer = Offer::find($id); // It's more efficient to use find() here instead of where()->first()

        if ($offer) {
            // Toggling the status between 'active' and 'inactive'
            $del = $offer->delete();

            if ($del) {
                return response()->json(['status'=>true,'message'=>'Success To Delete'],200);
                // return redirect()->back()->with('success', 'Success To Delete ');
            }

            return response()->json(['status'=>false,'message'=>'Faild To Delete'],200);
        }

        return response()->json(['status'=>false,'message'=>'This Offer Does Not Exist'],200);

    }
    
    public function offer_details(){
        $id=request('id');
        $offer=Offer::with('images','product')->where('id',$id)->first();
                $em=new StdClass();
foreach($offer->images as $img_obj){
    $img_obj->image=asset('storage').'/'.$img_obj->image;
            // $img=asset($img); 
        }
        return response()->json(['status'=>true,'message'=>'success','data'=>$offer??$em]);
    }
    public function delete_offer(){
        $id = request('id');
        $category = Offer::find($id); // It's more efficient to use find() here instead of where()->first()

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
    
    public function change_offer_status(){
        $id = request('id');
        $category = Offer::find($id); // It's more efficient to use find() here instead of where()->first()

        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $up = $category->update(['status' => $category->status == 'active' ? 'inactive' : 'active']);

            if ($up) {
                return redirect()->back()->with('success', 'تم تغير الحاله');
            }

            return redirect()->back()->with('error', 'لم يتم تغيير الحاله'); // Fixed typo here
        }

        return redirect()->back()->withErrors(['لم يتواجد']);
    }
    public function delete_offer_image(){
        $id=request('id');
        $img=Images::where('id',$id)->first();
        if($img){
            $del_one=$img->delete();
            if($del_one){
                return response()->json(['status'=>true,'message'=>'Success To Delete'],200);
            }
            return response()->json(['status'=>false,'message'=>'Faild To Delete'],200);
        }
        return response()->json(['status'=>false,'message'=>'This Element Does Not Exist'],200);

    }
    
}
