<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Enums\GenderEnum;
use App\Enums\ActiveStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function showUsers()
    {
        return view('admin.users');
    }

    public function index()
    {
        try {
            $users = User::select(
                'id','name','email','role','profile',
                'phone','city','gender','date_of_birth','status','created_at'
            )->get();

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
            Log::error("Failed to fetch user: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user'
            ], 500);
        }
    }

    // ================= CREATE =================
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
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $uploadedFiles = [];

        try {

            // ✅ Upload profile image
            if ($request->hasFile('profile')) {

                $file = $request->file('profile');

                $filename = Str::slug($request->name) . '_' . time() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('profile'), $filename);

                $data['profile'] = 'profile/' . $filename;

                $uploadedFiles[] = $data['profile'];
            }

            // 🔐 Hash password
            $data['password'] = Hash::make($request->password);

            $data['storage_path'] = 'users/' . uniqid();

            $user = User::create($data);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {

            $this->cleanupFiles($uploadedFiles);

            Log::error("Create user error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user'
            ], 500);
        }
    }

    // ================= UPDATE =================
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
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $data = $request->only([
                'name','email','role','phone','city',
                'gender','date_of_birth','status'
            ]);

            $newFiles = [];

            // ✅ Update profile image
            if ($request->hasFile('profile')) {

                // Delete old
                if ($user->profile && file_exists(public_path($user->profile))) {
                    unlink(public_path($user->profile));
                }

                $file = $request->file('profile');

                $filename = Str::slug($request->name) . '_' . time() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('profile'), $filename);

                $data['profile'] = 'profile/' . $filename;

                $newFiles[] = $data['profile'];
            } else {
                $data['profile'] = $user->profile;
            }

            // 🔐 Password update
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {

            foreach ($newFiles as $file) {
                $this->deleteFile($file);
            }

            Log::error("Update user error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user'
            ], 500);
        }
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            $this->deleteFile($user->profile);

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Delete error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
            ], 500);
        }
    }

    // ================= HELPERS =================
    private function deleteFile($file)
    {
        if ($file) {
            $path = public_path($file);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    private function cleanupFiles($files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }
}
