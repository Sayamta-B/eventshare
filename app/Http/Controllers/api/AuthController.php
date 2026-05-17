<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register_view(){
        return view('register');
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'organizer'
        ]);

        $token= $user->createToken('device_name')->plainTextToken;

        return response()->json([
            "token" => $token,
            "message" => "Successfully registered!"
        ], 201);
    }

    public function login_view(){
        return view('login');
    }

    public function login(Request $request){
        $user= User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                "message"=> "Invalid credentials!"
            ],401);
        }

        $token= $user->createToken($request->device_name?? 'web_token')->plainTextToken;

        return response()->json([
            "status"=> 200,
            "token" => $token,
            "role" => $user->role,
            "message"=> "Sucessfully logged in."
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "message"=>"Logged out sucessfully"
        ]);        
    }
}
