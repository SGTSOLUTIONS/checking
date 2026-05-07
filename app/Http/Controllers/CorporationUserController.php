<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Models\Corporation;
use App\Models\CorporationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class CorporationUserController extends Controller
{
    public function index()
    {
        $corporations = Corporation::all();
        $roles = RoleEnum::getValues();
        $genders = GenderEnum::getValues();
        $statuses = ActiveStatusEnum::getValues();

        return view('corporation-users.index', compact('corporations', 'roles', 'genders', 'statuses'));
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
            'role' => 'required|in:' . implode(',', RoleEnum::getValues()),
            'corporation_id' => 'required|exists:corporations,id',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|in:' . implode(',', GenderEnum::getValues()),
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:' . implode(',', ActiveStatusEnum::getValues()),
            'password' => 'required|string|min:6|confirmed',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile']);
        $data['password'] = Hash::make($request->password);
        $data['email_verified_at'] = now();

        // Handle profile upload to public directory
        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Create directory if not exists
            $uploadPath = public_path('uploads/corporation-users/profiles');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0777, true);
            }

            // Move file to public directory
            $file->move($uploadPath, $filename);
            $data['profile'] = 'uploads/corporation-users/profiles/' . $filename;
            $data['storage_path'] = 'uploads/corporation-users/profiles/' . $filename;
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
            'role' => 'required|in:' . implode(',', RoleEnum::getValues()),
            'corporation_id' => 'required|exists:corporations,id',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|in:' . implode(',', GenderEnum::getValues()),
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:' . implode(',', ActiveStatusEnum::getValues()),
            'password' => 'nullable|string|min:6',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle profile upload to public directory
        if ($request->hasFile('profile')) {
            // Delete old profile if exists
            if ($user->profile && File::exists(public_path($user->profile))) {
                File::delete(public_path($user->profile));
            }

            $file = $request->file('profile');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Create directory if not exists
            $uploadPath = public_path('uploads/corporation-users/profiles');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0777, true);
            }

            // Move file to public directory
            $file->move($uploadPath, $filename);
            $data['profile'] = 'uploads/corporation-users/profiles/' . $filename;
            $data['storage_path'] = 'uploads/corporation-users/profiles/' . $filename;
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

        // Delete profile image if exists from public directory
        if ($user->profile && File::exists(public_path($user->profile))) {
            File::delete(public_path($user->profile));
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Corporation user deleted successfully.'
        ]);
    }

    public function getRoles()
    {
        $roles = [];
        foreach (RoleEnum::cases() as $role) {
            $roles[] = [
                'value' => $role->value,
                'label' => $role->label()
            ];
        }

        return response()->json([
            'roles' => $roles
        ]);
    }

    public function getGenders()
    {
        $genders = [];
        foreach (GenderEnum::cases() as $gender) {
            $genders[] = [
                'value' => $gender->value,
                'label' => $gender->label()
            ];
        }

        return response()->json([
            'genders' => $genders
        ]);
    }

    public function getStatuses()
    {
        $statuses = [];
        foreach (ActiveStatusEnum::cases() as $status) {
            $statuses[] = [
                'value' => $status->value,
                'label' => $status->label()
            ];
        }

        return response()->json([
            'statuses' => $statuses
        ]);
    }
}
