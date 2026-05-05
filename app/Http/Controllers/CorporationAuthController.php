<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Models\CorporationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rule;

class CorporationAuthController extends Controller
{
    protected $guard = 'corporation';

    /**
     * Show Login Page
     */
    public function showLogin()
    {
        if (Auth::guard('corporation')->check()) {
            return redirect()->route('corporation.dashboard');
        }
        return view('corporation-auth.login');
    }

    /**
     * Handle Login (AJAX)
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Find user by email
        $user = CorporationUser::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No account found with this email.'
            ], 404);
        }

        // Ensure user is active
        if ($user->status !== ActiveStatusEnum::ACTIVE->value) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active. Please contact the administrator.'
            ], 403);
        }

        // Attempt login
        if (Auth::guard('corporation')->attempt($validated)) {
            $request->session()->regenerate();

            // Role-based redirect
            $redirect = route('corporation.dashboard');

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful! Welcome back.',
                'redirect' => $redirect,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid password. Please try again.'
        ], 401);
    }

    /**
     * Show Registration Page
     */
    public function showRegister()
    {
        if (Auth::guard('corporation')->check()) {
            return redirect()->route('corporation.dashboard');
        }
        return view('corporation-auth.register');
    }

    /**
     * Handle Registration (AJAX)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:corporation_users,email',
            'password' => 'required|min:6|confirmed',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'city' => 'required|string|max:255',
            'corporation_id' => 'required|exists:corporations,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle file upload to public directory
        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Create directory if not exists
            $uploadPath = public_path('uploads/corporation-users/profiles');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Move file to public directory
            $file->move($uploadPath, $filename);
            $profilePath = 'uploads/corporation-users/profiles/' . $filename;
        }

        // Create user
        $user = CorporationUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'city' => $validated['city'],
            'corporation_id' => $validated['corporation_id'],
            'profile' => $profilePath,
            'storage_path' => $profilePath,
            'email_verified_at' => now(),
            'status' => ActiveStatusEnum::ACTIVE->value,
            'role' => RoleEnum::DC->value // Default role for corporation users
        ]);

        // Auto login after registration
        Auth::guard('corporation')->login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful! Welcome to the portal.',
            'redirect' => route('corporation.dashboard')
        ]);
    }

    /**
     * Show Forgot Password Page
     */
    public function showForgotPassword()
    {
        return view('corporation-auth.forgot-password');
    }

    /**
     * Send Password Reset Link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Check if user exists
        $user = CorporationUser::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'We can\'t find a user with that email address.'
            ], 422);
        }

        try {
            // Generate reset token
            $token = Password::broker('corporation_users')->createToken($user);

            // Build reset URL
            $resetUrl = url(route('corporation.password.reset', [
                'token' => $token,
                'email' => $user->email
            ]));

            // Send email
            Mail::send('corporation-auth.emails.password-reset', [
                'resetUrl' => $resetUrl,
                'user' => $user
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset Request - Corporation Portal')
                    ->from(config('mail.from.address'), config('mail.from.name'));
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

    /**
     * Show Reset Password Page
     */
    public function showResetPassword(Request $request, $token = null)
    {
        return view('corporation-auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('corporation_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (CorporationUser $user, string $password) {
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
                'message' => 'Password has been reset successfully!',
                'redirect' => route('corporation.login')
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __($status)
        ], 422);
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return view('corporation-auth.dashboard');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('corporation')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('corporation.login');
    }
}
