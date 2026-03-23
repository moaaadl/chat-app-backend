<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                "unique:users,email",
            ],
            "password" => ["required", "string", "min:8", "confirmed"],
        ]);

        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
        ]);

        $token = $user->createToken("api-token")->plainTextToken;

        return response()->json(
            [
                "message" => "User registered successfully",
                "user" => $user,
                "token" => $token,
            ],
            201,
        );
    }
    public function login(Request $request)
    {
        $user = User::where("email", $request->email)->first();

        $token = $user->createToken("api-token")->plainTextToken;
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    "message" => "invalid Informations",
                ],
                401,
            );
        }
        return response()->json([
            "message" => "login success",
            "user" => $user,
            "token" => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(
            [
                "message" => "logout success!",
            ],
            200,
        );
    }
    public function users(Request $request)
{
    $users = User::where('id', '!=', auth()->id())->latest()->get();
    return response()->json($users);
}
}
