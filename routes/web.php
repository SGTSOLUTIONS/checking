<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommissionerController;
use App\Http\Controllers\CorporationController;
use App\Http\Controllers\CorporationUserController;
use App\Http\Controllers\DcController;
use App\Http\Controllers\SurveyorController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamLeaderController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use App\Models\CorporationUser;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\CorporationAuthController;

// Corporation Auth Routes

Route::prefix('corporation')->name('corporation.')->group(function () {
    Route::middleware('guest:corporation')->group(function () {
        Route::get('/login', [CorporationAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [CorporationAuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [CorporationAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [CorporationAuthController::class, 'register'])->name('register.submit');
        Route::get('/forgot-password', [CorporationAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [CorporationAuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [CorporationAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [CorporationAuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth:corporation')->group(function () {
        Route::get('/commissioner/dashboard', [CommissionerController::class, 'dashboard'])->name('dashboard');
        Route::get('/commissioner/ward/{ward_no}/map', [CommissionerController::class, 'mapView'])->name('ward.map');
        Route::post('/commissioner/update-assessment', [CommissionerController::class, 'updateAssessment'])->name('update.assessment');
        Route::get('/commissioner/get-assessment-details', [CommissionerController::class, 'getAssessmentDetails'])->name('get.assessment.details');
        Route::post('/commissioner/add-missing-columns', [CommissionerController::class, 'addMissingColumns'])->name('add.missing.columns');
        Route::post('/logout', [CorporationAuthController::class, 'logout'])->name('logout');
    });
});
Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/surveyor/track-location', [TrackingController::class, 'store']);

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

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

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
    // 📍 TRACKING ROUTES (Add these)
    // =============================

    // Tracking data routes - accessible by authenticated users
    Route::prefix('tracking')->name('tracking.')->group(function () {
        // Store tracking location (for surveyors)
        Route::post('/store', [TrackingController::class, 'store'])->name('store');

        // Get tracking data for specific user
        Route::get('/data/{userId?}', [TrackingController::class, 'getUserTracking'])->name('data');

        // Get tracking summary for dashboard
        Route::get('/summary', [TrackingController::class, 'getTrackingSummary'])->name('summary');
    });

    // IMPORTANT: Map view route (outside the prefix to avoid duplication)
    Route::get('/admin/tracking/map-view', [TrackingController::class, 'mapView'])->name('tracking.map-view');

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
            // Missing Bill Download Route
            Route::get('/wards/{id}/download-missing-bill', [WardController::class, 'downloadMissingBill']);
            Route::get('/wards/{id}/download-polygon', [WardController::class, 'downloadPolygon']);
            // User Management Routes
            Route::get('/users', [UserController::class, 'showUsers'])->name('admin.users');
            Route::get('/users/data', [UserController::class, 'index'])->name('admin.users.data');
            Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
            Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

            Route::get('/teams', [TeamController::class, 'showTeams'])->name('admin.team.index');
            Route::get('/teams/data', [TeamController::class, 'index'])->name('admin.teams.data');
            Route::post('/teams', [TeamController::class, 'store'])->name('admin.teams.store');
            Route::get('/teams/{id}/edit', [TeamController::class, 'edit'])->name('admin.teams.edit');
            Route::put('/teams/{id}', [TeamController::class, 'update'])->name('admin.teams.update');
            Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('admin.teams.destroy');

            // Team Members Management
            Route::get('/teams/{team}/available-surveyors', [TeamController::class, 'getAvailableSurveyors'])->name('admin.teams.available-surveyors');
            Route::post('/teams/{team}/add-member', [TeamController::class, 'addMember'])->name('admin.teams.add-member');
            Route::delete('/teams/{team}/remove-member/{user}', [TeamController::class, 'removeMember'])->name('admin.teams.remove-member');
            Route::get('/teams/{team}/load-roads', [TeamController::class, 'loadRoads'])->name('teams.load-roads');
            Route::post('/teams/assigned-roads', [TeamController::class, 'assignedRoads'])->name('teams.assigned-roads');
            Route::get('/teams/{team}/can-delete', [TeamController::class, 'canDeleteTeam'])->name('admin.teams.can-delete');
            Route::get('/teams/leader/{leader}', [TeamController::class, 'getTeamsByLeader'])->name('admin.teams.by-leader');
            Route::get('/tracking/map-view', [TrackingController::class, 'mapView'])->name('tracking.map-view');
            Route::get('/tracking/data/{userId?}', [TrackingController::class, 'getUserTracking'])->name('admin.tracking.data');

            Route::get('/corporation-user', [CorporationUserController::class, 'index'])->name('admin.corporation-user.index');
            Route::get('/corporation-user-list', [CorporationUserController::class, 'list'])->name('admin.corporation-user-list');
            Route::post('/corporation-user-store', [CorporationUserController::class, 'store'])->name('admin.corporation-user-store');
            Route::get('/corporation-user/{id}/edit', [CorporationUserController::class, 'edit']);
            Route::put('/corporation-user/{id}', [CorporationUserController::class, 'update']);
            Route::delete('/corporation-user/{id}', [CorporationUserController::class, 'destroy']);
            Route::get('/corporation-user-roles', [CorporationUserController::class, 'getRoles']);
            Route::get('/corporation-user-genders', [CorporationUserController::class, 'getGenders']);
            Route::get('/corporation-user-statuses', [CorporationUserController::class, 'getStatuses']);
        });

        Route::resource('users', UserController::class);
    });

    // =============================
    // 🗺️ Surveyor Routes
    // =============================
    Route::middleware('role:surveyor')->group(function () {
        Route::prefix('surveyor')->group(function () {
            Route::get('/dashboard', [SurveyorController::class, 'index'])->name('surveyor.dashboard');
            Route::get('/map-view', [SurveyorController::class, 'mapView'])->name('surveyor.mapview');

            Route::post('/save-features', [SurveyorController::class, 'saveFeatures'])->name('surveyor.save-features');
            Route::get('/get-features', [SurveyorController::class, 'getFeatures'])->name('surveyor.get-features');

            Route::post('/polygon-data-upload', [SurveyorController::class, 'uploadPolygonData'])->name('surveyor.polygon.datas.upload');
            Route::post('/point-data-upload', [SurveyorController::class, 'uploadPointData'])->name('surveyor.point.data.upload');

            Route::post('/update-road-name', [SurveyorController::class, 'updateRoadName'])->name('surveyor.update.road.name');
            Route::post('/delete-gisid', [SurveyorController::class, 'deleteGisid'])->name('surveyor.delgisid');

            Route::post('/add-polygon-feature', [SurveyorController::class, 'addPolygonFeature'])->name('surveyor.add.polygon.feature');
            Route::post('/add-line-feature', [SurveyorController::class, 'addLineFeature'])->name('surveyor.add.line.feature');
            Route::post('/add-point-feature', [SurveyorController::class, 'addPointFeature'])->name('surveyor.add.point.feature');
            Route::post('/modify-feature', [SurveyorController::class, 'modifyFeature'])->name('surveyor.modify.feature');
            Route::post('/delete-feature', [SurveyorController::class, 'deleteFeature'])->name('surveyor.delete.feature');
            Route::get('/progress', [SurveyorController::class, 'progress'])->name('surveyor.progress');



            Route::get('/search-ajax', [SurveyorController::class, 'searchPointData'])->name('surveyor.searchpointdata');
            Route::get('/point-data/{data}', [SurveyorController::class, 'getPointData'])->name('surveyor.point.data');
            Route::post('surveyor/update-point-record', [SurveyorController::class, 'updatePointRecord'])->name('surveyor.updatePointRecord');
            Route::post('surveyor/add-shop-record', [SurveyorController::class, 'addShopRecord'])->name('surveyor.addShopRecord');
            Route::post('surveyor/delete-shop-record', [SurveyorController::class, 'deleteShopRecord'])->name('surveyor.deleteShopRecord');
            Route::post('surveyor/update-point-record', [SurveyorController::class, 'updatePointRecord'])->name('surveyor.updatePointRecord');
            Route::post('surveyor/bulk-update-points', [SurveyorController::class, 'bulkUpdatePoints'])->name('surveyor.bulkUpdatePoints');
        });
    });

    // =============================
    // 👥 Team Leader Routes
    // =============================
    Route::middleware('role:team_leader')->group(function () {
        Route::prefix('teamleader')->group(function () {
            Route::get('/dashboard', [TeamLeaderController::class, 'dashboard'])->name('teamleader.dashboard');
            Route::delete('/teams/{team}/members/{member}', [TeamController::class, 'removeMember'])->name('teams.members.remove');
            Route::get('/teams/{team}/available-surveyors', [TeamController::class, 'getAvailableSurveyors'])->name('teams.available-surveyors');
            Route::post('/teams/{team}/add-member', [TeamController::class, 'addMember'])->name('teams.add-member');
            Route::post('/teams/missing-bill-download', [TeamController::class, 'downloadMissingBills'])->name('teams.missing-bill-download');
        });
    });
});

// =============================
// 🌐 Public Routes
// =============================
Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/send-whatsapp', [WardController::class, 'send']);
Route::get('/test-mail', function () {
    try {
        Mail::raw('Test email from SGT Property Survey', function ($message) {
            $message->to('sheikdawood13579@gmail.com')
                ->subject('Test Email');
        });
        return 'Mail sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});






Route::get('DC/Dashboard', [DcController::class, 'dashboard'])->name('dc.dashboard');
