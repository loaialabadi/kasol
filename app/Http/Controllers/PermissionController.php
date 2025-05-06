<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //
    public function permissions_page(){
        $permissions=Permission::get();
        return view('permissions/permissions_page',compact('permissions'));
    }
    public function add_new_permission(){

        return view('permissions.add_new_permission');
    }
    public function store_new_permission(Request $request)
{
    // Validate the incoming data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255|unique:permissions,name',
        // Uncomment and modify other fields as needed
        // 'guard_name' => 'required|string|max:255',
    ]);

    // Check if the user is authenticated
    $user = Auth::guard('admin')->user();
    if (!$user) {
        return redirect()->route('login');
    }

    // If validation passes, proceed with storing the new permission
    Permission::create([
        'name' => $validatedData['name'],
        // 'guard_name' => $validatedData['guard_name'], // Uncomment if necessary
    ]);

    // Redirect to permissions page with a success message
    return redirect()->route('permissions_page')->with('success', 'Permission created successfully');
}

    public function update_permission(){
        $id=request('id');
        $permission=Permission::find($id);
        // return $id;
        return view('permissions.update_permission_page',compact('id','permission'));
    }
    public function edit_permission(Request $request)
{
    // Validate the incoming data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255|unique:permissions,name,' . $request->id,
        // 'guard_name' => 'required|string|max:255', // Uncomment if needed
    ]);

    // Check if the user is authenticated
    $user = Auth::guard('admin')->user();
    if (!$user) {
        return redirect()->route('login');
    }

    // Find and update the permission
    $permission = Permission::find($request->id);
    if ($permission) {
        $permission->update([
            'name' => $validatedData['name'],
            // 'guard_name' => $validatedData['guard_name'], // Uncomment if needed
        ]);

        // Redirect to the permissions page with a success message
        return redirect()->route('permissions_page')->with('success', 'Permission updated successfully');
    }

    // If the permission wasn't found, redirect back with an error message
    return redirect()->route('permissions_page')->with('error', 'Permission not found');
}
    public function delete_permission(){
        $id = request('id');
        $user=Auth::guard('admin')->user();
        if(!$user){
            return redirect()->route('login');
        }
// return $id;
        $permission = Permission::where('id',$id)->delete();
        if ($permission) {
            // return $permission;
            // $permission->delete();

            return redirect()->back()->with('success', 'Permission deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Permission not found.');
        }
    }
}