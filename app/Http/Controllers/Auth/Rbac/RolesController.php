<?php

namespace App\Http\Controllers\Auth\Rbac;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use YaroslavMolchan\Rbac\Models\Permission;
use YaroslavMolchan\Rbac\Models\PermissionGroup;
use YaroslavMolchan\Rbac\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Role::all();


        return View('rbac.roles.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allpermissions = Permission::pluck('name','id')->toArray();
        $allgroups = PermissionGroup::pluck('name','id')->toArray();
        return view('rbac.roles.create')->with([
            'allpermissions'=> $allpermissions,
            'allgroups'=> $allgroups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'slug'     => 'required|max:255|unique:roles'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        } else {
            $model               = new Role;
            $model->name         = $request->input('name');
            $model->slug        = $request->input('slug');
            $model->save();
            if ($request->input('permission') != null) {
                foreach ($request->input('permission') as $permission){
                    $model->attachPermission(Permission::find($permission));
                }
            }

            if ($request->input('permission-group') != null) {
                foreach ($request->input('permission-group') as $permissiongroup){
                    $model->attachGroup(PermissionGroup::find($permissiongroup));
                }
            }
            return redirect('rbac/roles')->with('success', 'Successfully created role!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = Role::find($id);
        $permissions = $model->permissions()->get();
        $permissiongroups = $model->permissionGroups()->get();
        return view('rbac.roles.show')->with([
            'model'=> $model,
            'permissions'=> (count($permissions)>0) ? $permissions : false,
            'permissiongroups'=> (count($permissiongroups)>0) ? $permissiongroups : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return $this
     */
    public function edit($id)
    {
        $model = Role::find($id);
        $permissions = $model->permissions()->pluck('id')->toArray();
        $allpermissions = Permission::pluck('name','id')->toArray();
        $permissionsgroups = $model->permissionGroups()->pluck('id')->toArray();
        $allpermissionsgroups = PermissionGroup::pluck('name','id')->toArray();
        return view('rbac.roles.edit')->with([
            'model'=> $model,
            'permissions'=> $permissions,
            'allpermissions'=> $allpermissions,
            'permissionsgroups'=> $permissionsgroups,
            'allpermissionsgroups'=> $allpermissionsgroups
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'slug'     => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        } else {
            $model = Role::find($id);
            $model->name         = $request->input('name');
            $model->slug        = $request->input('slug');
            if ($request->input('permission-group') != null && $request->input('permission') != null) {

                $all_permissiongroups = PermissionGroup::all();

                $all_permissions = Permission::all();
                foreach ($all_permissiongroups as $permissiongroups){
                    $model->detachGroup($permissiongroups);
                }
                foreach ($all_permissions as $permission){
                    $model->detachPermission($permission->id);
                }

                foreach ($request->input('permission-group') as $permission){
                    $model->attachGroup(PermissionGroup::find($permission));
                }


                foreach ($request->input('permission') as $permission){
                    $model->detachPermission(Permission::find($permission));
                    $model->attachPermission(Permission::find($permission));
                }


            }elseif($request->input('permission-group') == null && $request->input('permission') != null){
                $all_permissiongroups = PermissionGroup::all();
                $all_permissions = Permission::all();
                foreach ($all_permissiongroups as $permissiongroups){
                    $model->detachGroup($permissiongroups);
                }
                foreach ($all_permissions as $permission){
                    $model->detachPermission($permission->id);
                }

                foreach ($request->input('permission') as $permission){
                    $model->attachPermission(Permission::find($permission));
                }
            }elseif ($request->input('permission-group') != null && $request->input('permission') == null){
                $all_permissiongroups = PermissionGroup::all();
                $all_permissions = Permission::all();
                foreach ($all_permissiongroups as $permissiongroups){
                    $model->detachGroup($permissiongroups);
                }
                foreach ($all_permissions as $permission){
                    $model->detachPermission($permission->id);
                }
                foreach ($request->input('permission-group') as $permission){
                    $model->attachGroup(PermissionGroup::find($permission));
                }
            }else{
                $all_permissiongroups = PermissionGroup::all();
                $all_permissions = Permission::all();
                foreach ($all_permissiongroups as $permissiongroups){
                    $model->detachGroup($permissiongroups);
                }
                foreach ($all_permissions as $permission){
                    $model->detachPermission($permission->id);
                }
            }

            $model->save();
            return redirect('rbac/roles')->with('success', 'Successfully updated role!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Role::find($id);
        $model->delete();
        return redirect('rbac/roles')->with('success','role has been  deleted');
    }
}
