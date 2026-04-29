<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatusEnum;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    /** Show Login Page */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /** Show Register Page */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /** Handle Login (AJAX) */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No account found with this email.'
            ], 404);
        }

        if ($user->status !== ActiveStatusEnum::ACTIVE->value) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active. Please contact the administrator.'
            ], 403);
        }

        if (Auth::attempt($validated, $request->remember ? true : false)) {
            $request->session()->regenerate();

            // Role-based redirect
            $redirect = match($user->role) {
                RoleEnum::ADMIN->value => route('admin.dashboard'),
                RoleEnum::TEAM_LEADER->value => route('teamleader.dashboard'),
                RoleEnum::SURVEYOR->value => route('surveyor.dashboard'),
                default => route('dashboard'),
            };

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful!',
                'redirect' => $redirect,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid password.'
        ], 401);
    }

    /** Handle Register (AJAX) */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'city' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle file upload
        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $request->email) . '.' . $file->getClientOriginalExtension();
            $profilePath = $file->storeAs('profile_pictures', $filename, 'public');
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'city' => $validated['city'],
            'profile_picture' => $profilePath,
            'status' => ActiveStatusEnum::ACTIVE->value,
            'role' => RoleEnum::SURVEYOR->value,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful! Please login to continue.',
            'redirect' => route('login')
        ]);
    }

    /** Dashboard */
    public function dashboard()
    {
        $user = Auth::user();

        return match($user->role) {
            RoleEnum::ADMIN->value => view('admin.dashboard'),
            RoleEnum::TEAM_LEADER->value => view('teamleader.dashboard'),
            RoleEnum::SURVEYOR->value => view('surveyor.dashboard'),
            default => view('taxpayer.dashboard'),
        };
    }

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function teamleaderDashboard()
    {
        return view('teamleader.dashboard');
    }

    public function surveyorDashboard()
    {
        return view('surveyor.dashboard');
    }

    /** Logout */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /** Show Forgot Password Page */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /** Send Password Reset Link */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'We can\'t find a user with that email address.'
            ], 404);
        }

        try {
            $token = Password::createToken($user);
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

            Mail::send('emails.password-reset', [
                'resetUrl' => $resetUrl,
                'user' => $user
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset Request - TN Municipal Portal');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset link has been sent to your email!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email. Please try again later.'
            ], 500);
        }
    }

    /** Show Reset Password Page */
    public function showResetPassword(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /** Reset Password */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully!',
                'redirect' => route('login')
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __($status)
        ], 422);
    }
}
