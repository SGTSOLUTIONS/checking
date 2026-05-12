<?php

namespace App\Http\Controllers;

use App\Models\Corporation;
use App\Models\CorporationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class CorporationAuthController extends Controller
{
    public function showLogin()
    {



        // Check corporation guard
        if (Auth::guard('corporation')->check()) {
            return redirect()->route('corporation.dashboard');
        }
  // Check regular users (web guard)
        if (Auth::guard('web')->check()) {

            $user = Auth::guard('web')->user();

            // Admin
            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Team Leader
            if ($user->role == 'team_leader') {
                return redirect()->route('teamleader.dashboard');
            }

            // Surveyor
            if ($user->role == 'surveyor') {
                return redirect()->route('surveyor.dashboard');
            }
        }
        return view('auth.login');

        // Check corporation guard


        return view('corporation-auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = CorporationUser::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No account found with this email.'
            ], 404);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is not active. Please contact administrator.'
            ], 403);
        }

        if (Auth::guard('corporation')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful! Welcome back.',
                'redirect' => route('corporation.dashboard')
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid password. Please try again.'
        ], 401);
    }

    public function showRegister()
    {
        if (Auth::guard('corporation')->check()) {
            return redirect()->route('corporation.dashboard');
        }

        $corporations = Corporation::all();

        return view('corporation-auth.register', compact('corporations'));
    }

    public function register(Request $request)
    {
        $request->validate([
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

        $profilePath = null;

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/corporation-users/profiles');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $filename);
            $profilePath = 'uploads/corporation-users/profiles/' . $filename;
        }

        $user = CorporationUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'city' => $request->city,
            'corporation_id' => $request->corporation_id,
            'profile' => $profilePath,
            'storage_path' => $profilePath,
            'email_verified_at' => now(),
            'status' => 'active',
            'role' => 'dc'
        ]);

        Auth::guard('corporation')->login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful! Welcome to the portal.',
            'redirect' => route('corporation.dashboard')
        ]);
    }

    public function showForgotPassword()
    {
        return view('corporation-auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = CorporationUser::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'We can\'t find a user with that email address.'
            ], 422);
        }

        try {
            $token = Password::broker('corporation_users')->createToken($user);
            $resetUrl = route('corporation.password.reset', [
                'token' => $token,
                'email' => $user->email
            ]);

            Mail::send('emails.corporation-password-reset', [
                'user' => $user,
                'resetUrl' => $resetUrl
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset Request - Corporation Portal');
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

    public function showResetPassword(Request $request, $token = null)
    {
        return view('corporation-auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('corporation_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
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


    public function logout(Request $request)
    {
        Auth::guard('corporation')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('corporation.login');
    }
}
