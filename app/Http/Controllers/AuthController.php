<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(AuthRequest $request)
    {
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
        ]);

        return response(["message" => "Registration succeeded!"], 201);
    }

    public function login(Request $request)
    {
        $vd = Validator::make($request->all(), [
            "email" => [
                "required",
                "email",
            ],
            "password" => [
                "required",
                "min:8"
            ]
        ]);

        $cd = $vd->validated();

        if (!Auth::attempt($cd)) {
            return response(["message" => "Login failed!"], 401);
        }

        $user = Auth::user();

        $token = $user->createToken(uniqid())->plainTextToken;

        return response([
            "message" => "Login succeeded",
            "user" => $user,
            "token" => $token
        ]);
    }
    public function me()
    {
        $user = Auth::user();

        return response([
            "data" => $user
        ], 200);
    }
    public function logout(Request $request)
    {
        $user = Auth::user();

        $user->tokens()->delete();
        
        $request->user()->currentAccessToken()->delete();

        return response(['message' => 'Logout successfully'], 200);
    }
}
