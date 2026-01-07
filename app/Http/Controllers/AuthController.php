<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        // Register the user name , email, password
        $credentials = $request->validated();
        $deviceName = $request->header('User-Agent', 'Unknown Device');
        $user = User::create($credentials);
        // Assign the member role while registering from the normal route
        $memberRole = Role::where('name', 'member')->first();
        if ($memberRole) {
            $user->roles()->attach($memberRole->id);
        }
        $user->load('roles');
        return response()->json([
            'message' => 'User registered',
            'access_token' => $user->createToken($deviceName)->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request) {
        // Login
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !(Hash::check($credentials['password'], $user->password))) {
            return response()->json([
                'message' => 'The entered credentials are incorrect',
            ], 401);
        }
        $deviceName = $request->header('User-agent', 'Unknown Device');
        $user->load('roles');
        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $user->createToken($deviceName)->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);

    }
    public function logout(Request $request){
        // Delete the token
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'You are logged out',
        ], 200);
    }
}
