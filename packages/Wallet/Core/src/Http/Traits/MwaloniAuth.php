<?php

namespace Wallet\Core\Http\Traits;

trait MwaloniAuth
{
    public function generateApiKey($lenth = 32)
    {
        return bin2hex(random_bytes($lenth));
    }

    public function generateApiUSername($user)
    {
        $user->update([
            'username' => strtolower($user->first_name . substr($user->last_name, 0, 2) . $user->id)
        ]);
        return true;
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
