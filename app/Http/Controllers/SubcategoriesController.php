<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
// use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubcategoriesController extends Controller
{
    public function categories_page(){
        $subcategories=SubCategory::paginate(10);
        // return $subcategories;
        return view('subcategories.subcategories_page',compact('subcategories'));
    }
    public function add_new_subcategory(){
        $id=request('id');
        // return $id;
        return view('subcategories.add_new_subcategory',compact('id'));
    }
    public function store_new_subcategory(Request $request){
        // return $request;
        $id=request('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors','أدخل كامل البيانات');
            // return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('subcategories', 'public') : null;
        $data=[];
        $data['image']=$imagePath;
        $data['name']=$request->name;
        $data['category_id']=$request->id;
        $new_one=SubCategory::create($data);
        if($new_one){
            return redirect()->route('category_subcategories',['id'=>$id])->with('success','تمت الاضافه');
        }
        return redirect()->route('category_subcategories',['id'=>$id])->with('faild','لم تتم الاضافه');
    }
    public function store_update_subcategory(Request $request){
        // return $request;
        $id=request('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors','أكمل البيانات');
            // return response()->json(['errors' => $validator->errors()], 422);
        }
        $data=[];
        $data['name']=$request->name;
        if($request->hasFile('image')){
            $imagePath = $request->hasFile('image') ? $request->file('image')->store('subcategories', 'public') : null;
            $data['image']=$imagePath;
        }
        $sub=SubCategory::where('id',$id)->first();
        // $data['category_id']=$request->id;
        $new_one=SubCategory::where('id',$id)->update($data);
        if($new_one){
            return redirect()->route('category_subcategories',['id'=>$sub->category_id])->with('success','تم التحديث');
        }
        return redirect()->route('category_subcategories',['id'=>$sub->category_id])->with('faild','فشل التحديث');
    }
    public function change_subcategory_status(){
        $id = request('id');
        $category = SubCategory::find($id); // It's more efficient to use find() here instead of where()->first()

        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $up = $category->update(['status' => $category->status == 'active' ? 'inactive' : 'active']);

            if ($up) {
                return redirect()->back()->with('success', 'تم التحديث');
            }

            return redirect()->back()->with('error', 'فشل التحديث'); // Fixed typo here
        }

        return redirect()->back()->withErrors(['لم يتواجد']);
    }
    public function delete_subcategory(){
        $id = request('id');
        $category = SubCategory::find($id); // It's more efficient to use find() here instead of where()->first()

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

    public function update_subcategory(){
        $id=request('id');
        $subcategory=SubCategory::find($id);
        // return $id;
        return view('subcategories.update_subcategory',compact('id','subcategory'));
    }


    public function search_subcategory(Request $request)
{
    $searchQuery = $request->get('search');
    $id=request('id');
    // Fetch subcategories based on the search query
//  return $id;
    $subcategories = SubCategory::where('name', 'LIKE', '%' . $searchQuery . '%')->where('category_id',$id)->paginate(10);

    // Return the results to the view
    return view('subcategories.subcategories_page', compact('subcategories','id'));
}

}