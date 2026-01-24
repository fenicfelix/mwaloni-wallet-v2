<?php

namespace Wallet\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'status'  => '01',
                'message' => 'Invalid credentials',
            ], Response::HTTP_OK);
        }

        $user = Auth::user();

        // Optional: delete old tokens if this is a service account
        // $user->tokens()->delete();

        $token = $user->createToken(
            'api-token',
            ['*'] // or scoped abilities like ['payments:create']
        );

        return response()->json([
            'status'  => '00',
            'message' => 'Success',
            'data' => [
                'token'      => $token->plainTextToken,
                'tokenType'  => 'Bearer',
                'expiresIn'  => null, // Sanctum does not embed TTL like JWT
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
