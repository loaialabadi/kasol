<?php

namespace App\Http\Controllers;

use App\Models\RolePermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Spatie\Permission\Models\Role;
use DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// use Spatie\permis;

class RulesController extends Controller
{
    public function roles_page(){
        $roles=Role::get();
        return view('rules.rules_page',data: compact('roles'));
    }
    public function add_new_role(){
        $permissions=Permission::get();
        return view('rules.add_new_role',compact('permissions'));
    }
    public function store_new_role(Request $request)
    {
        // return $request;
        // Validate the incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array', // Ensure permissions is an array
        ]);

        $user = Auth::guard('admin')->user();



        // If no user is authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // If validation passes, store the new role
        $role = Role::create([
            'name' => $validatedData['name'],
        ]);

        if($role){
            $permissions=$request->permissions;
        if(in_array('all',$permissions)){
            $permissions=Permission::get();
            foreach($permissions as $perm){
                $new=RolePermissions::insert([
                    'role_id'=>$role->id,
                    'permission_id'=>$perm->id,
                ]);
            }
        }
        }
        // Optionally, assign permissions if provided (you might want to associate them here)
        // Example: $role->permissions()->sync($validatedData['permissions']);

        // Redirect with success message
        return redirect()->route('roles_page')->with('success', 'تمت الاضافه بنجاح');
    }

    public function update_role(){
        $id=request('id');
        $role=Role::find($id);
        // return $id;
        return view('rules.update_role_page',compact('id','role'));
    }
    public function edit_role(Request $request){
        // return $request;
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            // 'guard_name' => 'required|string|max:255',
        ]);
        $user=Auth::guard('admin')->user();
        if(!$user){
            return redirect()->route('login');
        }
        Role::where('id',$request->id)->update([
            'name' => $validatedData['name'],
            // 'guard_name' => $validatedData['guard_name'],
        ]);
        return redirect()->route('roles_page')->with('success', 'تمت الاضافه بنجاح');
        // return $request;
    }
    public function delete_role(){
        $id = request('id');
        $user=Auth::guard('admin')->user();
        if(!$user){
            return redirect()->route('login');
        }
// return $id;
        $role = Role::where('id',$id)->delete();
        if ($role) {
            // return $role;
            // $role->delete();

            return redirect()->back()->with('success', 'تم المسح بنجاح');
        } else {
            return redirect()->back()->with('error', 'لم يتم المسح.');
        }
    }
    public function roles_pemissions(){
        $id=request('id');
        // $permissions=;
        $permissions=RolePermissions::with('permission')->where('role_id',$id)->get();
        return view('rules.roles_pemissions',compact('id','permissions'));
    }

    public function assign_permission_page(){
        $id=request('id');
        // $permissions=;
        $permissions_ids=RolePermissions::where('role_id',$id)->pluck('permission_id')->toArray();
        $permissions=Permission::whereNotIn('id',$permissions_ids)->get();
        // $permissions=RolePermissions::with('permission')->where('role_id',$id)->get();
        return view('rules.assign_permission_page',compact('id','permissions'));
    }

    public function store_assigned_permission(Request $request){
        // return $request;
        $request->validate([
            'role_id' => 'required|exists:roles,id', // Ensure role_id exists in the roles table
            'permission_id' => 'required|exists:permissions,id', // Ensure permission_id exists in the permissions table
        ]);

        $data=[];
        $data['role_id']=$request->role_id;
        $data['permission_id']=$request->permission_id;
        // return $data;
        $new=RolePermissions::create($data);
        return redirect()->back()->with('success', 'تم المسح!');
    }
    public function delete_permission($role_id, $permission_id)
    {
        // Find and delete the permission record based on the role_id and permission_id
        $del = RolePermissions::where(['role_id' => $role_id, 'permission_id' => $permission_id])->delete();

        // Check if deletion was successful
        if ($del) {
            return redirect()->back()->with('success', 'تم المسح!');
        } else {
            return redirect()->back()->with('error', 'لم يتم المسح.');
        }
    }
}