<?php

namespace Wallet\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NCBACallbackController extends Controller
{
    public function ncba_kplc_callback(Request $request, $id)
    {
        // Log the incoming request for debugging
        Log::info('NCBA KPLC Callback received: ' . json_encode($request->all()));
        Log::info('NCBA KPLC Callback ID: ' . $id);

        // Process the callback data
        // You can implement your logic here to handle the callback

        // Return a response to acknowledge receipt of the callback
        return response()->json(['status' => 'success'], 200);
    }
}
