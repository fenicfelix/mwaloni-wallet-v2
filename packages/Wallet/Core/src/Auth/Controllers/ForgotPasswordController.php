<?php

namespace Wallet\Core\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        // Middleware or other initializations can be added here
    }

    public function create()
    {
        // Return the admin login form view
        return view('core::auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        // Handle admin login
    }
}
