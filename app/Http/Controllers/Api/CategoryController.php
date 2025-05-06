<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
     
     public function categories_page(){
        $categories=Category::paginate(10);
        // return $categories;
        return view('categories.categories_page',compact('categories'));
    }
    public function add_new_category(){
        return view('categories.add_new_category');
    }
    public function update_category(){
        $id=request('id');
        $category=Category::where('id',$id)->first();
        return view('categories.update_category',compact('category'));
    }

public function store_update_category(Request $request){
        // return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data=[];
        if($request->hasFile('image')){
            $imagePath = $request->hasFile('image') ? $request->file('image')->store('categories', 'public') : null;
            $data['image']=$imagePath;
        }
        $data['name']=$request->name;
        $up_one=Category::where('id',$request->id)->update($data);
        if($up_one){
            return redirect()->route('categories_page')->with('success','تم التحديث');
        }
        return redirect()->route('categories_page')->with('error','لم يتم التحديث');

    }
    public function store_new_category(Request $request){
        // return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('categories', 'public') : null;
        $data=[];
        $data['image']=$imagePath;
        $data['name']=$request->name;
        $new_one=Category::create($data);
        if($new_one){
            return redirect()->route('categories_page')->with('success','تمت الاضافه');
        }
        return redirect()->route('categories_page')->with('faild','لم تتم الاضافه');
    }
    public function change_category_status(){
        $id = request('id');
        $category = Category::find($id); // It's more efficient to use find() here instead of where()->first()

        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $up = $category->update(['status' => $category->status == 'active' ? 'inactive' : 'active']);

            if ($up) {
                return redirect()->back()->with('success', 'تم التغيير');
            }

            return redirect()->back()->with('error', 'لم يتم التغيير'); // Fixed typo here
        }

        return redirect()->back()->withErrors(['لم يتواجد']);
    }
    public function delete_categories(){
        $id = request('id');
        $category = Category::find($id); // It's more efficient to use find() here instead of where()->first()
 $check_services=Service::where('category_id',$id)->first();
 if($check_services){
     return redirect()->back()->with('error','يوجد خدمات بها');
 }
        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $del = $category->delete();

            if ($del) {
                return redirect()->back()->with('success', 'تم الحذف');
            }

            return redirect()->back()->with('error', 'لم يتم الحذف '); // Fixed typo here
        }

        return redirect()->back()->withErrors(['لم يتواجد']);
    }


    public function searchCategories(Request $request)
    {
        $searchQuery = $request->get('search');

        // Fetch categories based on the search query
        $categories = Category::where('name', 'LIKE', '%' . $searchQuery . '%')->paginate(10);

        // Return the results to the view
        return view('categories.categories_page', compact('categories'));
    }
    public function category_subcategories(){
        $id=request('id');
        $subcategories=SubCategory::where('category_id',$id)->paginate(10);
        return view('subcategories.subcategories_page',compact('subcategories','id'));
    }
     
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

            $categories = Category::with('subCategories')->where('status','active')->paginate($perPage, ['*'], 'page', $page);

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'massage' => 'Categories fetched successfully',
                'data' => CategoryResource::collection($categories),
                'pagination' => [
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                    'current_page' => $categories->currentPage(),
                    // 'last_page' => $categories->lastPage(),
                    'pages_count' => $categories->lastPage(),
                ],
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories',
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

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $category = Category::with('subCategories')->findOrFail($id);

            return response()->json([
                'success' => true,
                'massage' => 'Category fetched successfully',
                'data' => new CategoryResource($category)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
