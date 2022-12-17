<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Wallet;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all()->map(function ($user) {
            $user->wallet = Wallet::where('user_id', $user->id)->first();
            return $user;
        });
        return response()->json($users);
        
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ],
            [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'email.unique' => 'Email is already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $wallet = Wallet::create([
                'balance' => 0,
                'user_id' => $user->id,
            ]);
            $response = [
                'user' => $user,
                'wallet' => $wallet,
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User registration failed! '.$e->getMessage()
            ], 409);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        $user->wallet = Wallet::where('user_id', $user->id)->first();
        return $user;
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        try{
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'password' => 'required|min:6'
            ],
            [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'email.unique' => 'Email is already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User update failed! '.$e->getMessage()
            ], 409);
        }
        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json('User deleted!');
    }

}
