<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\AnnouncementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('change-password', [AuthController::class, 'changePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
    });
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Public announcements
Route::prefix('announcements')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
    Route::get('/urgent', [AnnouncementController::class, 'urgent']);
    Route::get('/latest', [AnnouncementController::class, 'latest']);
    Route::get('/{announcement}', [AnnouncementController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // User info route
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });

    // Complaint routes
    Route::prefix('complaints')->group(function () {
        Route::get('/', [ComplaintController::class, 'index']);
        Route::post('/', [ComplaintController::class, 'store']);
        Route::get('/statistics', [ComplaintController::class, 'statistics']);
        Route::get('/categories', [ComplaintController::class, 'categories']);
        Route::get('/{complaint}', [ComplaintController::class, 'show']);
        Route::put('/{complaint}', [ComplaintController::class, 'update']);
        Route::delete('/{complaint}', [ComplaintController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin API Routes (Future Implementation)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Admin-specific API endpoints can be added here
    // For example: statistics, bulk operations, etc.

    Route::get('/dashboard-stats', function () {
        $stats = [
            'total_complaints' => \App\Models\Complaint::count(),
            'total_users' => \App\Models\User::role('user')->count(),
            'pending_complaints' => \App\Models\Complaint::where('status', 'pending')->count(),
            'completed_today' => \App\Models\Complaint::where('status', 'completed')
                ->whereDate('updated_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| API Documentation Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Lurah/RW Complaint Management System API',
        'version' => '1.0.0',
        'documentation' => url('/api/docs'),
        'endpoints' => [
            'authentication' => [
                'POST /auth/register' => 'Register new user',
                'POST /auth/login' => 'Login user',
                'GET /auth/profile' => 'Get user profile',
                'PUT /auth/profile' => 'Update user profile',
                'PUT /auth/change-password' => 'Change password',
                'POST /auth/logout' => 'Logout from current device',
                'POST /auth/logout-all' => 'Logout from all devices',
            ],
            'announcements' => [
                'GET /announcements' => 'Get all announcements',
                'GET /announcements/urgent' => 'Get urgent announcements',
                'GET /announcements/latest' => 'Get latest announcements',
                'GET /announcements/{id}' => 'Get specific announcement',
            ],
            'complaints' => [
                'GET /complaints' => 'Get user complaints',
                'POST /complaints' => 'Create new complaint',
                'GET /complaints/statistics' => 'Get user complaint statistics',
                'GET /complaints/categories' => 'Get complaint categories',
                'GET /complaints/{id}' => 'Get specific complaint',
                'PUT /complaints/{id}' => 'Update complaint',
                'DELETE /complaints/{id}' => 'Delete complaint',
            ]
        ]
    ]);
});
