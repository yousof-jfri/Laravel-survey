<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // register a new user
    public function register(Request $request)
    {
        // validate
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        // save user into the data base
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        // create token
        $token = $user->createToken('main')->plainTextToken;

        // return response
        return response([
            'user' => $user,
            'token' => $token
        ]);
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
            'remember' => 'boolean',
        ]);

        $remember = $credentials['remember'] ?? false;

        unset($credentials['remember']);

        if(!Auth::attempt($credentials, $remember)) {
            return response([
                'error' => 'the provided_credentials are not correct',
            ], 422);
        }

        
        $user = Auth::user();
        Auth::loginUsingId($user->id);
        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(){
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }
}
