<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CorporationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamLeaderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// =============================
// 🔒 Guest Routes (Only for Non-Logged-in Users)
// =============================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// =============================
// 🔐 Authenticated Routes (Logged-in Users Only)
// =============================
Route::middleware('auth')->group(function () {
    // Common Dashboard (for all roles)
    Route::get('/dashboards', [AuthController::class, 'dashboards'])->name('dashboards');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // =============================
    // 🧭 Admin Routes (Only Admin Role)
    // =============================
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/mapExplore', [AdminController::class, 'mapExplore'])->name('admin.mapExplore');
        Route::get('/corporation-data', [CorporationController::class, 'corporationData'])->name('admin.corporationdata');
        Route::post('/corporation-data', [CorporationController::class, 'corporationStore'])->name('admin.corporation-store');
        Route::get('/admin/corporation/list', [CorporationController::class, 'corporationList'])->name('admin.corporation-list');
        Route::get('/admin/corporation/{id}/edit', [CorporationController::class, 'edit'])->name('corporation-edit');
        Route::put('/admin/corporation/{id}', [CorporationController::class, 'update'])->name('corporation-update');
        Route::delete('/admin/corporation/{id}', [CorporationController::class, 'destroy'])->name('corporation-destroy');




        //ward
        Route::prefix('admin')->group(function () {
            Route::get('ward/{corporation_id}', [WardController::class, 'wards'])->name('admin.wards');
            Route::get('/wards/{corporationId}/data', [WardController::class, 'index'])->name('admin.wards.index');
            Route::get('/wards/{id}/edit', [WardController::class, 'edit'])->name('admin.wards.edit');
            Route::post('/wards', [WardController::class, 'store'])->name('admin.wards.store');
            Route::put('/wards/{id}', [WardController::class, 'update'])->name('admin.wards.update');
            Route::delete('/wards/{id}', [WardController::class, 'destroy'])->name('admin.wards.destroy');

            // User Management Routes
            Route::get('/users', [UserController::class, 'showUsers'])->name('admin.users');
            Route::get('/users/data', [UserController::class, 'index'])->name('admin.users.data');
            Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');


            Route::get('/teams', [TeamController::class, 'index'])->name('admin.team.index');
            Route::get('/teams/data', [TeamController::class, 'getTeamsData'])->name('admin.teams.data');
            Route::get('/teams/create', [TeamController::class, 'create'])->name('admin.teams.create');
            Route::post('/teams', [TeamController::class, 'store'])->name('admin.teams.store');
            Route::get('/teams/{id}/edit', [TeamController::class, 'edit'])->name('admin.teams.edit');
            Route::put('/teams/{id}', [TeamController::class, 'update'])->name('admin.teams.update');
            Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('admin.teams.destroy');
        });




        // User Management CRUD
        Route::resource('users', UserController::class);
    });
});
Route::get('/welcome', function () {
    return view('welcome');
});
