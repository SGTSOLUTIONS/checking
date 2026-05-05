<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Models\Corporation;
use App\Models\CorporationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CorporationUserController extends Controller
{
    public function index()
    {
        $corporations = Corporation::all();
        return view('corporation-users.index', compact('corporations'));
    }

    public function list(Request $request)
    {
        $users = CorporationUser::with('corporation')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:corporation_users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', array_column(RoleEnum::cases(), 'value')),
            'corporation_id' => 'required|exists:corporations,id',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|in:' . implode(',', array_column(GenderEnum::cases(), 'value')),
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:' . implode(',', array_column(ActiveStatusEnum::cases(), 'value')),
            'password' => 'required|string|min:6|confirmed',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('profile')) {
            $path = $request->file('profile')->store('corporation-users/profiles', 'public');
            $data['profile'] = $path;
            $data['storage_path'] = $path;
        }

        $user = CorporationUser::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Corporation user created successfully.',
            'data' => $user->load('corporation')
        ]);
    }

    public function edit($id)
    {
        $user = CorporationUser::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = CorporationUser::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('corporation_users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', array_column(RoleEnum::cases(), 'value')),
            'corporation_id' => 'required|exists:corporations,id',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|in:' . implode(',', array_column(GenderEnum::cases(), 'value')),
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:' . implode(',', array_column(ActiveStatusEnum::cases(), 'value')),
            'password' => 'nullable|string|min:6|confirmed',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile')) {
            // Delete old profile if exists
            if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                Storage::disk('public')->delete($user->profile);
            }
            $path = $request->file('profile')->store('corporation-users/profiles', 'public');
            $data['profile'] = $path;
            $data['storage_path'] = $path;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Corporation user updated successfully.',
            'data' => $user->load('corporation')
        ]);
    }

    public function destroy($id)
    {
        $user = CorporationUser::findOrFail($id);

        // Delete profile image if exists
        if ($user->profile && Storage::disk('public')->exists($user->profile)) {
            Storage::disk('public')->delete($user->profile);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Corporation user deleted successfully.'
        ]);
    }

    public function getRoles()
    {
        return response()->json([
            'roles' => array_column(RoleEnum::cases(), 'value')
        ]);
    }

    public function getGenders()
    {
        return response()->json([
            'genders' => array_column(GenderEnum::cases(), 'value')
        ]);
    }

    public function getStatuses()
    {
        return response()->json([
            'statuses' => array_column(ActiveStatusEnum::cases(), 'value')
        ]);
    }
}
