<?php

namespace Wallet\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wallet\Core\Jobs\Momo\ProcessMomoCallback;

class MomoCallbackController extends Controller
{
    public function disbursement(Request $request)
    {
        $json = $request->json()->all();
        ProcessMomoCallback::dispatch($json)->onQueue("trx-callback");
        return response()->json(['status' => 'success'], 200);
    }
}
