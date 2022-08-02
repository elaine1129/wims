<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{

    /**
     * Get a JWT via given credentials
     * 
     * @return \Illuminate\HTTP\JsonResponse
     */
    public function login(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'username' => 'required | exists:users,username',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('username', $req->username)->first();
        if ($user == null || $user->status !== "ACTIVE") {
            return response()->json(['error' => 'Your account is not active'], 401);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Log the user out (invalidate the token)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure
     * 
     * @param string $token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            // 'user' => new UserResource(User::findOrFail(Auth::id()))
            'user' => auth()->user()
        ]);
    }
}
