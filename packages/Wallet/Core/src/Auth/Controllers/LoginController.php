<?php

namespace Wallet\Core\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    public function create()
    {
        // Return the admin login form view
        return view('core::auth.login');
    }

    public function store(Request $request)
    {
        // Handle admin login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        // Handle admin logout
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        session()->flash('success', 'Logged out successfully.');

        return redirect()->route('vendly.admin.login');
    }
}
