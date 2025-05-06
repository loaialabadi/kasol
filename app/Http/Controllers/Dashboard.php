<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;

// use Auth;
class Dashboard extends Controller
{
    //
    public function index()
    {
        $user=Auth::guard('admin')->user();
        // return $user;
        if($user==null){
            return redirect()->route('login');
        }
        $admin=Auth::guard('admin')->user();
        $active_users=User::where('is_verified',1)->count();
        $total_users=User::count();
        // return $users;
        // return 'reer';
        $recent_orders=Order::with('user')->orderBy('created_at','Desc')->take(10)->get();
        // return $recent_orders;
        // return $admin;
        return view('home',compact('active_users','admin','total_users','recent_orders'));
        // return 'index';
    }
}