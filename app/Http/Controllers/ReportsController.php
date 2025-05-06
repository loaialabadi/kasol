<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ReportsController extends Controller
{
        public function reports_page(){
        $reports=Report::with('user','service')->get();
        return view('reports.reports_page',compact('reports'));
        // return $reports;
    }
    public function make_report(Request $request){
        // $user=Auth::user();
        
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        if($accessToken==null){
            return response()->json([
                    'success' => false,
                    'message' => 'Session Ended Login Again',
                ],401);
        }
        if($accessToken==null){
            return response()->json(['status'=>false,'message'=>'Session  Ended'],401);
        }
                $validatedData = $request->validate([
            'text'    => 'required|string',           // Limit the text length
        ]);
        $new_repo_data=[
            'text'=>$request->text,
            'service_id'=>$request->service_id,
            'user_id'=>$accessToken->tokenable_id,
        ];
        $new_repo=Report::create($new_repo_data);
        if($new_repo){
            return response()->json(['status'=>true,'message'=>'تم إضافه الشكوى بنجاح'],200);
        }
        return response()->json(['status'=>false,'message'=>'لم يتم إضافه الشكوى'],203);
    }
        public function delete_report(){
        $id = request('id');
        $category = Report::find($id); // It's more efficient to use find() here instead of where()->first()

        if ($category) {
            // Toggling the status between 'active' and 'inactive'
            $del = $category->delete();

            if ($del) {
                return redirect()->back()->with('success', 'Success To Delete ');
            }

            return redirect()->back()->with('error', 'Failed To Delete '); // Fixed typo here
        }

        return redirect()->back()->withErrors(['Not Found']);
    }
}