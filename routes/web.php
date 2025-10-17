<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{announcement:slug}', [AnnouncementController::class, 'show'])->name('announcements.show');

// Authenticated routes for announcements
Route::middleware('auth')->group(function () {
    Route::post('/announcements/{announcement}/comments', [AnnouncementController::class, 'storeComment'])->name('announcements.comments.store');
});

/*
|--------------------------------------------------------------------------
| User Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ComplaintController::class, 'dashboard'])->name('dashboard');

    // User Complaint Routes
    Route::resource('complaints', ComplaintController::class)->except(['destroy']);
    Route::get('/complaints/{complaint}/track', [ComplaintController::class, 'track'])->name('keluhan.track');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/complaints', [ReportController::class, 'complaints'])->name('reports.complaints');
    Route::get('/reports/users', [ReportController::class, 'users'])->name('reports.users');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Complaint Management
    Route::resource('complaints', AdminComplaintController::class);
    Route::get('complaints/{complaint}/print', [AdminComplaintController::class, 'print'])->name('complaints.print');
    Route::patch('complaints/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.status');
    Route::post('complaints/{complaint}/response', [AdminComplaintController::class, 'addResponse'])->name('complaints.response');
    Route::delete('attachments/{attachment}', [AdminComplaintController::class, 'deleteAttachment'])->name('attachments.delete');
    Route::get('complaints/export/pdf', [AdminComplaintController::class, 'exportPdf'])->name('complaints.export.pdf');
    Route::get('complaints/export/excel', [AdminComplaintController::class, 'exportExcel'])->name('complaints.export.excel');

    // Category Management
    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
    Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');

    // User Management
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
    Route::patch('users/{user}/unverify-email', [UserController::class, 'unverifyEmail'])->name('users.unverify-email');
    Route::patch('users/{user}/verify-user', [UserController::class, 'verifyUser'])->name('users.verify-user');
    Route::patch('users/{user}/reject-verification', [UserController::class, 'rejectVerification'])->name('users.reject-verification');
    Route::patch('users/{user}/change-role', [UserController::class, 'changeRole'])->name('users.change-role');
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');

    // Announcement Management
    Route::resource('announcements', AdminAnnouncementController::class);
    Route::patch('announcements/{announcement}/toggle-status', [AdminAnnouncementController::class, 'toggleStatus'])->name('announcements.toggle-status');
    Route::patch('announcements/{announcement}/toggle-urgent', [AdminAnnouncementController::class, 'toggleUrgent'])->name('announcements.toggle-urgent');
    Route::post('announcements/{announcement}/publish', [AdminAnnouncementController::class, 'publish'])->name('announcements.publish');
    Route::post('announcements/{announcement}/unpublish', [AdminAnnouncementController::class, 'unpublish'])->name('announcements.unpublish');
    Route::post('announcements/{announcement}/duplicate', [AdminAnnouncementController::class, 'duplicate'])->name('announcements.duplicate');
    Route::post('announcements/bulk-action', [AdminAnnouncementController::class, 'bulkAction'])->name('announcements.bulk-action');
    Route::get('announcements/export', [AdminAnnouncementController::class, 'export'])->name('announcements.export');
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
    Route::get('/profile/export', [ProfileController::class, 'export'])->name('profile.export');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
