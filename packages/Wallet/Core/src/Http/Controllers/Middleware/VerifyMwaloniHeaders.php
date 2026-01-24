<?php

namespace Wallet\Core\Http\Controllers\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMwaloniHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('x-api-key');

        info('RAW_REQUEST: '.json_encode($request->all()));

        if (! $apiKey) {
            return response()->json([
                'status' => '01',
                'message' => 'Unauthorized: API key missing',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $account = User::where('api_key', $apiKey)->first();

        if (! $account) {
            return response()->json([
                'status' => '01',
                'message' => 'Unauthorized: Invalid API key',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // ✅ Attach metadata instead of overriding auth user
        $request->attributes->set('api_account', $account);

        return $next($request);
    }
}
