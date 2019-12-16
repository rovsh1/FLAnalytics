<?php

namespace App\Http\Controllers\Auth\Rbac;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use YaroslavMolchan\Rbac\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Permission::all();


        return View('rbac.permissions.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rbac.permissions.create');
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
            'slug'     => 'required|max:255|unique:permissions'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        } else {
            $model               = new Permission;
            $model->name         = $request->input('name');
            $model->slug        = $request->input('slug');
            $model->save();
            return redirect('rbac/permissions')->with('success', 'Successfully created permission!');
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
        $model = Permission::find($id);

        return view('rbac.permissions.show')->with(['model'=> $model]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Permission::find($id);
        return view('rbac.permissions.edit')->with(['model'=> $model]);
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
            $model = Permission::find($id);
            $model->name         = $request->input('name');
            $model->slug        = $request->input('slug');
            $model->save();
            return redirect('rbac/permissions')->with('success', 'Successfully updated permission!');
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
        $model = Permission::find($id);
        $model->delete();
        return redirect('rbac/permissions')->with('success','permission has been  deleted');
    }
}
