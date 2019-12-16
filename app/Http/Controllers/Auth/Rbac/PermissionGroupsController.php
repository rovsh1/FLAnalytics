<?php

namespace App\Http\Controllers\Auth\Rbac;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use YaroslavMolchan\Rbac\Models\Permission;
use YaroslavMolchan\Rbac\Models\PermissionGroup;

class PermissionGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = PermissionGroup::all();


        return View('rbac.permissionGroups.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allpermissions = Permission::pluck('name','id')->toArray();
        return view('rbac.permissionGroups.create')->with('allpermissions', $allpermissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        var_dump($request->all());exit;
        $validator = Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'module'     => 'required|max:255|unique:permission_groups'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        } else {
            $model               = new PermissionGroup;
            $model->name         = $request->input('name');
            $model->module        = $request->input('module');
            $model->save();
            if ($request->input('permission') != null) {
                foreach ($request->input('permission') as $permission){
                    $model->attachPermission(Permission::find($permission));
                }
            }
            return redirect('rbac/permission-groups')->with('success', 'Successfully created permission!');
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
        $model = PermissionGroup::find($id);
        $permissions = $model->permissions()->get();
        return view('rbac.permissionGroups.show')->with([
            'model'=> $model,
            'permissions'=> (count($permissions)>0) ? $permissions : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = PermissionGroup::find($id);
        $permissions = $model->permissions()->pluck('id')->toArray();
        $allpermissions = Permission::pluck('name','id')->toArray();
        return view('rbac.permissionGroups.edit')->with([
            'model'=> $model,
            'permissions'=> $permissions,
            'allpermissions'=> $allpermissions
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
            'module'     => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        } else {
            $model = PermissionGroup::find($id);
            $model->name         = $request->input('name');
            $model->module        = $request->input('module');
            if ($request->input('permission') != null) {

                $all_permissions = Permission::all();

                foreach ($all_permissions as $permission){
                    $model->detachPermission($permission->id);
                }
                foreach ($request->input('permission') as $permission){
                    $model->attachPermission(Permission::find($permission));
                }
            }else{
                $all_permissions = Permission::all();

                foreach ($all_permissions as $permission){
                    $model->detachPermission($permission->id);
                }
            }

            $model->save();
            return redirect('rbac/permission-groups')->with('success', 'Successfully updated permission group!');
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
        $model = PermissionGroup::find($id);
        $model->delete();
        return redirect('rbac/permission-groups')->with('success','group has been  deleted');
    }
}
