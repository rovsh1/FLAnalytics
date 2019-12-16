<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;
use Validator;
use App\User;
use Auth;
use YaroslavMolchan\Rbac\Models\Role;

class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware('role:admin',['except' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();


        return View('users.show-users', compact('users'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allroles = Role::pluck('name','id')->toArray();
        return view('users.create-user')->with('allroles', $allroles);
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
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ;
        } else {
            $user               = new User;
            $user->name         = $request->input('name');
            $user->email        = $request->input('email');
            $user->password     = bcrypt($request->input('password'));
            $user->save();
            if ($request->input('role') != null) {
                foreach ($request->input('role') as $role){
                    $user->attachRole(Role::find($role));
                }
            }
            return redirect('users')->with('success', 'Successfully created user!');
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

        $user = User::find($id);

        return view('users.show-user')->withUser($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = $user->roles()->pluck('id')->toArray();
        $allroles = Role::pluck('name','id')->toArray();
        return View('users.edit-user', compact('user', 'roles','allroles' ));
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

        $user        = User::find($id);
        $emailCheck  = ($request->input('email') != '') && ($request->input('email') != $user->email);
        $passwordCheck = true;


        if ($emailCheck) {
            $validator = Validator::make($request->all(), [
                'name'      => 'present|max:255',
                'email'     => 'nullable|email|max:255|unique:users',
                'password'  => 'present|confirmed|min:6'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name'      => 'required|max:255',
                'password'  => 'nullable|confirmed|min:6'
            ]);
        }
//        var_dump($validator->errors());exit;
        if ($validator->fails() || $passwordCheck == false) {
            return back()
                ->withErrors($validator)
                ;
        } else {
            if(!empty($request->input('name'))){
                $user->name = $request->input('name');
            }
            if ($emailCheck) {
                $user->email = $request->input('email');
            }
            if ($request->input('password') != null) {
                $user->password = bcrypt($request->input('password'));
            }
            if ($request->input('role') != null) {

                $all_roles = Role::all();

                foreach ($all_roles as $role){
                    $user->detachRole($role->id);
                }
                foreach ($request->input('role') as $role){
                    $user->attachRole(Role::find($role));
                }
            }
            $user->save();
            return back()->with('success', 'Successfully updated user');
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

        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        if ($currentUser != $user) {
            $user->delete();
            return redirect('users')->with('success', 'Successfully deleted the user!');
        }
        return back()->with('error', 'You cannot delete yourself!');

    }
}
