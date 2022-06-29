<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $credentials = $req->validate([
            'username' => 'required | exists:users,username',
            'password' => 'required'
        ]);
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            // $req->session()->regenerate();
            $user = Auth::user();
            $token = $user->createToken('main')->plainTextToken;

            return response([
                'user' => $user,
                'token' => $token
            ]);
        }
        return response([
            'error' => 'The provided credentials are not correct'
        ], 422);
        // if (!Auth::attempt($credentials, $remember)) {
        //     return response([
        //         'error' => 'The provided credentials are not correct'
        //     ], 422);
        // }
        // /** @var \App\Models\User $user **/
        // if (Auth::attempt($credentials, $remember)) {
        //     return redirect()->intended('/');
        // }
        // $user = Auth::user();
        // $token = $user->createToken('main')->plainTextToken;

        // return response([
        //     'user' => $user,
        //     'token' => $token
        // ]);
    }

    public function logout()
    {
        // /** @var \App\Models\User $user */
        // $user = Auth::user();

        // $user->currentAccessToken()->delete();

        // return response([
        //     'success' => true
        // ]);
    }
}
