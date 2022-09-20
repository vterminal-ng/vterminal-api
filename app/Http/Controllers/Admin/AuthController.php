<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function getLogin()
    {
        return view('admin.login');
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $credentials['role'] = 'admin';

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
 
            return redirect()->intended('admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Auth::logout();

        // Revoke current user tokens (Still confirm if you need this)
        auth()->user()->tokens()->delete();
 
        $request->session()->invalidate();
     
        $request->session()->regenerateToken();

        // redirect the login page
        return redirect()->route('admin.login');
    }
}
