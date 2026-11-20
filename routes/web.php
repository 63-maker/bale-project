<?php

use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminComplaintController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Management\ManagementComplaintController;
use App\Http\Controllers\Management\ManagementDashboardController;
use App\Http\Controllers\Management\ManagementNotificationController;
use App\Http\Controllers\Student\StudentComplaintController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - College Complaint Management System (CCMS)
|--------------------------------------------------------------------------
*/

// Direct Application Entry - Login UI appears first
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login/student', [AuthController::class, 'loginStudent'])->name('login.student');
    Route::post('/login/management', [AuthController::class, 'loginManagement'])->name('login.management');
    Route::post('/login/admin', [AuthController::class, 'loginAdmin'])->name('login.admin');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'registerStudent'])->name('register.post');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('password.update');
});

// Student Portal Routes
Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/complaints', [StudentComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/new', [StudentComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [StudentComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [StudentComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/comments', [StudentComplaintController::class, 'addComment'])->name('complaints.comments.store');
    Route::post('/complaints/{complaint}/reopen', [StudentComplaintController::class, 'reopen'])->name('complaints.reopen');
    Route::get('/attachments/{attachment}/download', [StudentComplaintController::class, 'downloadAttachment'])->name('attachments.download');

    Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [StudentNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [StudentNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Management Portal Routes
Route::middleware(['auth', 'active', 'role:management', 'password.must_change'])->prefix('management')->name('management.')->group(function () {
    Route::get('/dashboard', [ManagementDashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/assigned', [ManagementComplaintController::class, 'index'])->name('assigned.index');
    Route::get('/complaints/{complaint}', [ManagementComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/start', [ManagementComplaintController::class, 'startInvestigation'])->name('complaints.start');
    Route::post('/complaints/{complaint}/comments', [ManagementComplaintController::class, 'addComment'])->name('complaints.comments.store');
    Route::post('/complaints/{complaint}/resolution', [ManagementComplaintController::class, 'submitResolution'])->name('complaints.resolution.store');
    Route::post('/complaints/{complaint}/complete-closure', [ManagementComplaintController::class, 'completeClosure'])->name('complaints.complete-closure');
    Route::get('/verification-status', [ManagementComplaintController::class, 'verificationStatus'])->name('verification-status');
    Route::get('/closed', [ManagementComplaintController::class, 'closed'])->name('closed');
    Route::get('/attachments/{attachment}/download', [ManagementComplaintController::class, 'downloadAttachment'])->name('attachments.download');

    Route::get('/notifications', [ManagementNotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [ManagementNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [ManagementNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Admin Portal Routes
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{complaint}', [AdminComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/assign', [AdminComplaintController::class, 'assign'])->name('complaints.assign');
    Route::post('/complaints/{complaint}/auto-assign', [AdminComplaintController::class, 'triggerAutoAssign'])->name('complaints.auto-assign');
    Route::get('/verification-queue', [AdminComplaintController::class, 'showVerificationQueue'])->name('verification-queue');
    Route::post('/complaints/{complaint}/verify', [AdminComplaintController::class, 'verify'])->name('complaints.verify');
    Route::post('/complaints/{complaint}/reject', [AdminComplaintController::class, 'rejectComplaint'])->name('complaints.reject');
    Route::post('/complaints/{complaint}/force-close', [AdminComplaintController::class, 'forceClose'])->name('complaints.force-close');
    Route::post('/complaints/{complaint}/comments', [AdminComplaintController::class, 'addComment'])->name('complaints.comments.store');
    Route::get('/attachments/{attachment}/download', [AdminComplaintController::class, 'downloadAttachment'])->name('attachments.download');

    Route::get('/management', [AdminManagementController::class, 'index'])->name('management.index');
    Route::post('/management', [AdminManagementController::class, 'store'])->name('management.store');
    Route::put('/management/{management}', [AdminManagementController::class, 'update'])->name('management.update');
    Route::patch('/management/{management}/status', [AdminManagementController::class, 'toggleStatus'])->name('management.status');

    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::patch('/students/{student}/status', [AdminStudentController::class, 'toggleStatus'])->name('students.status');

    Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');
    Route::patch('/departments/{department}/status', [AdminDepartmentController::class, 'toggleStatus'])->name('departments.status');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}/status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.status');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [AdminReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [AdminReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
