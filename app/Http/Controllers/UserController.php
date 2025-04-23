<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required', // Tambahkan validasi name
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role' => 'required'
        ]);
    
        User::create([
            'name' => $request->name, // Tambahkan name
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);
    
        return redirect()->route('users.index');
    }
    

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}
