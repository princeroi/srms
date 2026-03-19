<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|min:8',
        ]);

        $throttleKey = Str::lower($request->email).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Too many attempts. Try again in {$seconds} seconds."]);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::hit($throttleKey, 60);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        session(['last_activity_time' => time()]);

        return $this->redirectByRole(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('message', 'Logged out successfully.');
    }

    public function redirectByRole($user)
    {
        return redirect()->to(match(true){
            $user->hasRole('super_admin')                                           => '/superadmin',
            $user->hasAnyRole(['hr_admin_specialist', 'hr_manager'])                => '/hr',
            $user->hasAnyRole(['operation_specialist', 'operation_manager'])        => '/operation',
            $user->hasAnyRole(['payroll_specialist', 'payroll_manager'])            => '/payroll',
            $user->hasAnyRole(['finance_specialist', 'finance_manager'])            => '/finance',
            $user->hasAnyRole(['purchasing_specialist'])                            => '/purchasing',
            default                                                                 => '/login',
        });
    }
}
