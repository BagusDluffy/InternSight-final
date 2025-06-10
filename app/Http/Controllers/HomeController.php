<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('prevent-back-history');
    }

    // Show the dashboard page
    public function index()
    {
        $magangCount = Magang::count();
        return view('page.dashboard', compact('magangCount'));
    }

    // Show the login form
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('layoutsauth.login');
    }

    // Handle user login
    public function dologin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus di isi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus di isi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/home')
                ->with('success', 'Berhasil login!');
        }

        return back()
            ->with('error', 'Email atau password salah')
            ->withInput($request->except('password'));
    }

    // Handle user logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Berhasil logout!');
    }

    // Registration methods remain the same...
}
