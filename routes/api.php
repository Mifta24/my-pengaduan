<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\NotificationController;
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

    // Dashboard
    Route::get('/dashboard', [ComplaintController::class, 'dashboard']);

    // Complaint routes
    Route::prefix('complaints')->group(function () {
        Route::get('/', [ComplaintController::class, 'index']);
        Route::post('/', [ComplaintController::class, 'store']);
        Route::get('/statistics', [ComplaintController::class, 'statistics']);
        Route::get('/categories', [ComplaintController::class, 'categories']);
        Route::get('/{complaint}', [ComplaintController::class, 'show']);
        Route::get('/{complaint}/track', [ComplaintController::class, 'track']);
        Route::put('/{complaint}', [ComplaintController::class, 'update']);
        Route::delete('/{complaint}', [ComplaintController::class, 'destroy']);
    });

    // Announcement comments
    Route::post('/announcements/{announcement}/comments', [AnnouncementController::class, 'storeComment']);

    // Device Token routes (for FCM)
    Route::prefix('device-tokens')->group(function () {
        Route::post('/', [DeviceTokenController::class, 'store']);
        Route::get('/', [DeviceTokenController::class, 'index']);
        Route::delete('/{id}', [DeviceTokenController::class, 'destroy']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // Notification Settings routes
    Route::prefix('notification-settings')->group(function () {
        Route::get('/', [NotificationController::class, 'getSettings']);
        Route::put('/', [NotificationController::class, 'updateSettings']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Api\Admin\ReportController as AdminReportController;

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/quick-stats', [DashboardController::class, 'quickStats']);

    // Complaint Management
    Route::prefix('complaints')->group(function () {
        Route::get('/', [AdminComplaintController::class, 'index']);
        Route::post('/', [AdminComplaintController::class, 'store']);
        Route::get('/statistics', [AdminComplaintController::class, 'statistics']);
        Route::get('/{id}', [AdminComplaintController::class, 'show']);
        Route::put('/{id}', [AdminComplaintController::class, 'update']);
        Route::delete('/{id}', [AdminComplaintController::class, 'destroy']);
        Route::patch('/{id}/status', [AdminComplaintController::class, 'updateStatus']);
        Route::post('/{id}/resolve', [AdminComplaintController::class, 'markAsResolved']);
        Route::post('/{id}/response', [AdminComplaintController::class, 'addResponse']);
        Route::delete('/attachments/{id}', [AdminComplaintController::class, 'deleteAttachment']);
        Route::post('/bulk-update', [AdminComplaintController::class, 'bulkUpdate']);
    });

    // Category Management
    Route::prefix('categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index']);
        Route::get('/active', [AdminCategoryController::class, 'active']);
        Route::get('/{id}', [AdminCategoryController::class, 'show']);
        Route::post('/', [AdminCategoryController::class, 'store']);
        Route::put('/{id}', [AdminCategoryController::class, 'update']);
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [AdminCategoryController::class, 'toggleStatus']);
        Route::post('/bulk-action', [AdminCategoryController::class, 'bulkAction']);
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index']);
        Route::get('/{id}', [AdminUserController::class, 'show']);
        Route::post('/', [AdminUserController::class, 'store']);
        Route::put('/{id}', [AdminUserController::class, 'update']);
        Route::delete('/{id}', [AdminUserController::class, 'destroy']);
        Route::patch('/{id}/verify-email', [AdminUserController::class, 'verifyEmail']);
        Route::patch('/{id}/verify-user', [AdminUserController::class, 'verifyUser']);
        Route::patch('/{id}/change-role', [AdminUserController::class, 'changeRole']);
        Route::patch('/{id}/reset-password', [AdminUserController::class, 'resetPassword']);
    });

    // Announcement Management
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AdminAnnouncementController::class, 'index']);
        Route::get('/{id}', [AdminAnnouncementController::class, 'show']);
        Route::post('/', [AdminAnnouncementController::class, 'store']);
        Route::put('/{id}', [AdminAnnouncementController::class, 'update']);
        Route::delete('/{id}', [AdminAnnouncementController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [AdminAnnouncementController::class, 'toggleStatus']);
        Route::patch('/{id}/toggle-sticky', [AdminAnnouncementController::class, 'toggleSticky']);
        Route::post('/{id}/publish', [AdminAnnouncementController::class, 'publish']);
        Route::post('/{id}/unpublish', [AdminAnnouncementController::class, 'unpublish']);
    });

    // Report Management
    Route::prefix('reports')->group(function () {
        Route::get('/overview', [AdminReportController::class, 'overview']);
        Route::get('/complaints', [AdminReportController::class, 'complaints']);
        Route::get('/users', [AdminReportController::class, 'users']);
        Route::post('/export', [AdminReportController::class, 'export']);
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
