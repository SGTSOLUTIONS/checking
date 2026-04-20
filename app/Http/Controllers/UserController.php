<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Enums\GenderEnum;
use App\Enums\ActiveStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    public function showUsers()
    {
        return view('admin.users');
    }

    public function index()
    {
        try {
            $users = User::select('id', 'name', 'email', 'role', 'profile', 'phone', 'city', 'gender', 'date_of_birth', 'status', 'created_at')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load users: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load users'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch user details: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user details'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', new Enum(RoleEnum::class)],
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'gender' => ['nullable', new Enum(GenderEnum::class)],
            'date_of_birth' => 'nullable|date',
            'status' => ['required', new Enum(ActiveStatusEnum::class)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $uploadedFiles = [];

        try {
            // Handle profile image - Store in profile folder
            if ($request->hasFile('profile')) {
                // Generate a unique filename
                $filename = time() . '_' . uniqid() . '.' . $request->file('profile')->getClientOriginalExtension();
                $data['profile'] = $request->file('profile')
                    ->storeAs('profile', $filename, 'public');
                $uploadedFiles[] = $data['profile'];
            }

            // Hash password
            $data['password'] = Hash::make($request->password);

            // Set storage path
            $data['storage_path'] = 'users/' . uniqid();

            $user = User::create($data);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            $this->cleanupFiles($uploadedFiles);
            Log::error("Failed to create user: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', new Enum(RoleEnum::class)],
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'gender' => ['nullable', new Enum(GenderEnum::class)],
            'date_of_birth' => 'nullable|date',
            'status' => ['required', new Enum(ActiveStatusEnum::class)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only([
                'name',
                'email',
                'role',
                'phone',
                'city',
                'gender',
                'date_of_birth',
                'status'
            ]);

            $newFiles = [];

            // 🖼️ Handle Profile Image - Store in profile folder
            if ($request->hasFile('profile')) {
                // Delete old profile image if exists
                if ($user->profile) {
                    Storage::disk('public')->delete($user->profile);
                }

                // Generate a unique filename
                $filename = time() . '_' . uniqid() . '.' . $request->file('profile')->getClientOriginalExtension();
                $path = $request->file('profile')->storeAs('profile', $filename, 'public');
                $data['profile'] = $path;
                $newFiles[] = $path;
            } else {
                $data['profile'] = $user->profile;
            }

            // 🔐 Handle Password (optional)
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // 🧠 Fix: if status, role, or gender come as array
            foreach (['status', 'role', 'gender'] as $field) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    $data[$field] = $data[$field][0];
                }
            }

            // 🧩 Make sure status is properly assigned
            if (isset($data['status'])) {
                $user->status = $data['status'];
            }

            // 🧩 Fill other details and save
            $user->fill($data);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Rollback uploaded files on error
            foreach ($newFiles as $file) {
                Storage::disk('public')->delete($file);
            }

            Log::error("Failed to update user", ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Delete profile image if exists
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete user: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up uploaded files
     */
    private function cleanupFiles($files)
    {
        foreach ($files as $file) {
            try {
                Storage::disk('public')->delete($file);
            } catch (\Exception $e) {
                Log::error("Failed to delete file {$file}: " . $e->getMessage());
            }
        }
    }
}
