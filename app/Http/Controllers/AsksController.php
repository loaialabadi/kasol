<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AskResource;
use App\Models\Ask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsksController extends Controller
{
    
    public function asks_page(){
        $asks=Ask::get();
        return view('asks.asks_page',compact('asks'));
    }
    public function add_new_ask(){
        return view('asks.add_new_ask');
    }
    public function update_ask(){
        $id=request('id');
        $ask=Ask::where('id',$id)->first();
        return view('asks.update_ask',compact('ask'));
    }
    public function store_new_ask(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data=[
            'title'=>$request->title,
            'description'=>$request->description,
        ];
        $new_one=Ask::create($data);
        if($new_one){
            return redirect()->route('asks_page')->with('success','تمت الاضافه');
        }
        return redirect()->back()->with('error','لم يتم الاضافه');
    }
    public function store_update_ask(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
            // return response()->json(['errors' => $validator->errors()], 422);
        }
        $data=[
            'title'=>$request->title,
            'description'=>$request->description,
        ];
        $id=$request->id;
        $up_one=Ask::where('id',$id)->update($data);
        if($up_one){
            return redirect()->route('asks_page')->with('success','تم التحديث');
        }
        return redirect()->back()->with('error','لم يتم التحديث');
    }
    public function delete_ask(){
        $id=request('id');
        $del=Ask::where('id',$id)->delete();
        if($del){
            return redirect()->back()->with('success','تم المسح');
        }
        return redirect()->back()->with('error','لم يتم المسح');
    }
    
    public function index(Request $request)
    {
        $asksQuery = Ask::query();


        $asks = $asksQuery->where('status',1)->get();

        return response()->json([
            'success' => true,
            'message' => 'Asks fetched successfully',
            'data' => AskResource::collection($asks)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ask_category_id' => 'required|exists:ask_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ask = Ask::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ask created successfully',
            'data' => new AskResource($ask)
        ]);
    }

    public function show(Ask $ask)
    {
        return response()->json([
            'success' => true,
            'data' => new AskResource($ask)
        ]);
    }

    public function update(Request $request, Ask $ask)
    {
        $validated = $request->validate([
            'ask_category_id' => 'required|exists:ask_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ask->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ask updated successfully',
            'data' => new AskResource($ask)
        ]);
    }

    public function destroy(Ask $ask)
    {
        $ask->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ask deleted successfully'
        ]);
    }
}