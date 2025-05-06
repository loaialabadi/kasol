<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// use Session;
use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    // Admin Registration
    public function login_page()
    {
        // return 'erer4334';
        return view('auth.login');
    }
    public function add_new_admin(){
        return view('admins.add_new_admin');
    }
    public function admins_page(){
        $admins=Admin::get();
        // return $admins;
        return view('admins.admins_page',compact('admins'));
    }
    public function delete_admin(){
        $id=request('id');
        $del=Admin::where('id',$id)->delete();
        if($del){
            return redirect()->back()->with('success','success To Delete');
        }
        return redirect()->back()->with('error', 'Faild To Delete');

    }
    public function update_admin_page(){
        $id=request('id');
        $admin=Admin::where('id',$id)->first();
        return view('admins.update_admin_page',compact('admin','id'));
    }

    public function store_new_admin(Request $request)
    {

        // return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'image' => 'nullable',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('admins', 'public') : null;
// return $imagePath;
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'image' => $imagePath,
            'age' => $request->age,
            'gender' => $request->gender ?? 'other',
        ]);
        return redirect()->route('admins_page');
        // return response()->json(['message' => 'Admin registered successfully', 'admin' => $admin], 201);
    }

    public function store_update_admin(Request $request)
    {
        // Validator for inputs
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('admins')->ignore($request->id), // Ignore the current admin's email for updates
            ],
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'image' => 'nullable|image', // Ensure the image is valid
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
            'password' => 'nullable|string', // Password should be optional but validated if present
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $adminData = [
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            // 'image' => $imagePath,
            'age' => $request->age,
            'gender' => $request->gender,
        ];
        // Handle image upload (if exists)
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('admins', 'public') ;
            $adminData['image']=$imagePath;
        }

        // Prepare data to update

// return $request;
        // Update password if provided
        if ($request->filled('password')) {
            $adminData['password'] = Hash::make($request->password); // Hash password if provided
        }

        // Update the admin's data
        $updateSuccess = Admin::where('id', $request->id)->update($adminData);

        if ($updateSuccess) {
            return redirect()->route('admins_page')->with('success', 'Admin updated successfully!');
        } else {
            return redirect()->route('admins_page')->with('error', 'Failed to update admin.');
        }
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:8',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('admins', 'public') : null;

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'image' => $imagePath,
            'age' => $request->age,
            'gender' => $request->gender ?? 'other',
        ]);

        return response()->json(['message' => 'Admin registered successfully', 'admin' => $admin], 201);
    }


    // Admin Login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('admin-api-token')->plainTextToken;

            return response()->json(['message' => 'Login successful', 'token' => $token, 'admin' => $admin], 200);
        }
        //
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Show Admin Profile
    public function showProfile()
    {
        $admin = Auth::guard('admin')->user();
        return response()->json(['admin' => $admin], 200);
    }

    // Update Admin Profile
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:admins,email,' . $admin->id,
            'address' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'age' => 'sometimes|integer|min:0',
            'gender' => 'sometimes|in:male,female,other',
            'password' => 'sometimes|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($admin->image && \Storage::disk('public')->exists($admin->image)) {
                \Storage::disk('public')->delete($admin->image);
            }
            $admin->image = $request->file('image')->store('admins', 'public');
        }

        $admin->update([
            'name' => $request->name ?? $admin->name,
            'email' => $request->email ?? $admin->email,
            'address' => $request->address ?? $admin->address,
            'phone' => $request->phone ?? $admin->phone,
            'age' => $request->age ?? $admin->age,
            'gender' => $request->gender ?? $admin->gender,
            'password' => $request->password ? Hash::make($request->password) : $admin->password,
        ]);

        return response()->json(['message' => 'Profile updated successfully', 'admin' => $admin], 200);
    }

    // Reset Admin Password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'User logged out successfully.'], 200);
    }

    public function admin_login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('admin-api-token')->plainTextToken;
            // return $admin;
            Auth::login($admin);
            $request->session()->regenerate();
            Session::flash('success', trans('LoginSuccessfully'));
            $request->session()->put('user', Auth::user());
            // Redirect to the admin dashboard
            return redirect()->route('dashboard')->with('success', 'Login successful!');
            // return response()->json(['message' => 'Login successful', 'token' => $token, 'admin' => $admin], 200);
        }
        return redirect()->back()->withErrors('Invalied Credential');
        // return response()->json(['message' => 'Invalid credentials'], 401);
    }
public function admin_log()
{
    auth()->guard('admin')->logout();
    return redirect()->route('login');
}
}
