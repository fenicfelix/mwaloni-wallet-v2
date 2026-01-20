<?php

namespace Wallet\Core\Http\Controllers\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMwaloniHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('x-api-key');

        if (!$apiKey) {
            return response()->json([
                'status' => '01',
                'message' => 'Unauthorized: API key missing',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return response()->json([
                'status' => '01',
                'message' => 'Unauthorized: Invalid API key',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Optionally, you can attach the user to the request for later use
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
