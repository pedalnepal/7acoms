<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CustomerUser;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // -------------------------
    // Register
    // -------------------------
    public function showRegister()
    {
        return view('front.user.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:customer_users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = CustomerUser::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Send verification email
        event(new Registered($user));

        // Auto login
        Auth::guard('customer')->login($user);

        return redirect()->route('user.dashboard')
            ->with('success', 'Registration successful.');
    }

    // -------------------------
    // Login / Logout
    // -------------------------
    public function showLogin()
    {
        return view('front.user.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('user.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }

    // -------------------------
    // Forgot Password
    // -------------------------
    public function showForgotPassword()
    {
        return view('front.user.forget-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // IMPORTANT: use your broker name from config/auth.php => "customer_users"
        $status = Password::broker('customer_users')->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // -------------------------
    // Reset Password
    // -------------------------
    public function showResetPassword(Request $request, string $token)
    {
        return view('front.user.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker('customer_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {

                // ✅ hash password (your current code was plain text)
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                // optional auto login
                Auth::guard('customer')->login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('user.dashboard')->with('status', 'Password reset successfully.')
            : back()->withErrors(['email' => __($status)]);
    }

    // -------------------------
    // Email Verification
    // -------------------------
    public function verifyEmailPublic(Request $request, int $id, string $hash)
    {
        $user = CustomerUser::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect()->route('user.login')->with('status', 'Email verified. Please login.');
    }

    public function verificationNotice()
    {
        return view('front.user.verification');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('user.dashboard')->with('status', 'Email verified successfully.');
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user(); // customer user via auth:customer

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('user.dashboard');
        }

        $user->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent.');
    }
}
