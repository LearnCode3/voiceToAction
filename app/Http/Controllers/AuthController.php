<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ------------------------------------------------------------------
    // Login
    // ------------------------------------------------------------------

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if banned before attempting
        $user = User::where('email', $credentials['email'])->first();
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact the administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect staff to office panel, super admin to admin panel
            $authUser = Auth::user();
            if ($authUser->isSuperAdmin()) {
                return redirect()->route('admin.offices.index');
            }
            if ($authUser->isOfficeStaff()) {
                return redirect()->route('office.panel');
            }
            return redirect()->intended(route('dashboard')); //This is the normal user(citizen) redirect
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    // ------------------------------------------------------------------
    // Register
    // ------------------------------------------------------------------

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => 'user',
            'is_active' => true,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
    }

    // ------------------------------------------------------------------
    // Logout
    // ------------------------------------------------------------------

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // ------------------------------------------------------------------
    // Forgot Password
    // ------------------------------------------------------------------

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // Check user exists & is active
        $user = User::where('email', $request->email)->first();
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'This account has been suspended.',
            ])->onlyInput('email');
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'A password reset link has been sent to your email address.')
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    // ------------------------------------------------------------------
    // Reset Password
    // ------------------------------------------------------------------

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password reset successfully! Please sign in.')
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }
}
