<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage users|view users|create users|edit users|delete users');
    }
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.user.list', ['users'=>$users, 'title'=>'Users List']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.form', ['title'=>'Add User']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','min:6'],
        ]);

        $user = new User;
        $user->fill($request->only(['name','email']));
        $user->password  = Hash::make($request->get('password'));
        $user->image = $request->get('image'); // if you store image field
        $user->save();

        return redirect(route('user.edit', $user->id))->with('success_message', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.user.form', ['user'=>$user, 'title' => 'Edit User']);
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
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable','min:6'],
        ]);

        $user->fill($request->only(['name','email']));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->image = $request->get('image'); // if applicable
        $user->save();

        \Session::flash('success_message', 'User Detail successfully updated.');
        return redirect(route('user.edit', $user->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        \Session::flash('success_message', 'User deleted successfully.');
        return redirect(route('user.index'));
    }
}
