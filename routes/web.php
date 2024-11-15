<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Business\BusinessEmployeeController;
use App\Http\Controllers\Business\BusinessCourseController;
use App\Http\Controllers\Business\BusinessReportController;
use App\Http\Controllers\Business\BusinessCourseAllocationController;
use App\Http\Controllers\Business\BusinessCoursePurchaseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\Business\BusinessCertificateController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/home', [HomeController::class, 'index'])->name('home');  // Add this line
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

//login + register routes

Route::middleware('guest')->group(function () {
    // Registration
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});




// Authentication routes (if you're using Laravel's built-in auth)
require __DIR__.'/auth.php';

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Course enrollment and interaction
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::post('{course}/enroll', [CourseEnrollmentController::class, 'enroll'])->name('enroll');
        Route::get('{course}/preview', [CourseController::class, 'preview'])->name('preview');
        Route::get('{course}/view', [CourseController::class, 'show'])->name('view');
        Route::post('{course}/sections/{courseSection}/complete', [SectionController::class, 'complete'])
            ->name('complete-section');
        Route::post('{course}/sections/{section}/next', [SectionController::class, 'next'])
            ->name('next-section');
        Route::post('{course}/sections/{section}/previous', [SectionController::class, 'previous'])
            ->name('previous-section');
    });

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
    });

    Route::get('/api/sections/{section}', [SectionController::class, 'show']);
    Route::post('/api/sections/{section}/mark-current', [SectionController::class, 'markCurrent']);
});




// Admin routes (add this inside your existing admin routes group, around line 74)
Route::middleware(['auth', CheckRole::class.':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminCourseController::class, 'dashboard'])->name('dashboard');

    // Courses Management
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [AdminCourseController::class, 'courses'])->name('index');
        Route::get('/create', [AdminCourseController::class, 'create'])->name('create');
        Route::post('/store', [AdminCourseController::class, 'store'])->name('store');
        Route::get('/{course}/edit', [AdminCourseController::class, 'edit'])->name('edit');
        Route::put('/{course}', [AdminCourseController::class, 'update'])->name('update');
        Route::delete('/{course}', [AdminCourseController::class, 'destroy'])->name('destroy');
        Route::get('/{course}/preview', [AdminCourseController::class, 'preview'])->name('preview');
        Route::post('/{course}/publish', [AdminCourseController::class, 'publish'])->name('publish');
        Route::post('/{course}/unpublish', [AdminCourseController::class, 'unpublish'])->name('unpublish');
    });
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])
    ->name('dashboard');

    // Users Management (add this new section)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });
});




// Business routes
Route::middleware(['auth', CheckRole::class.':business'])->prefix('business')->name('business.')->group(function () {
    Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [BusinessController::class, 'profile'])->name('profile');
    Route::put('/profile', [BusinessController::class, 'update'])->name('profile.update');
    Route::get('/analytics', [BusinessController::class, 'analytics'])->name('analytics');

    // Employee management
    Route::resource('employees', BusinessEmployeeController::class);
    Route::post('employees/{employee}/allocate-course', [BusinessEmployeeController::class, 'allocateCourse'])->name('business.employees.allocate-course');

    // Course purchasing
    Route::resource('courses', BusinessCourseController::class)->only(['index']);
    Route::get('/courses/available', [BusinessCourseController::class, 'available'])->name('courses.available');
    Route::post('/courses/{course}/purchase', [BusinessCourseController::class, 'purchase'])->name('courses.purchase');

    // Course allocation
    Route::get('/courses/purchased', [BusinessCourseController::class, 'purchased'])->name('courses.purchased');
    Route::get('/courses/{purchase}/allocate', [BusinessCourseController::class, 'showAllocationForm'])
        ->name('courses.showAllocationForm');
    Route::post('/courses/{purchase}/allocate', [BusinessCourseController::class, 'allocate'])
        ->name('courses.allocate');

    // Reports
    Route::get('/reports/progress', [BusinessReportController::class, 'progress'])->name('reports.progress');

    // Business course allocation routes
    Route::resource('allocations', BusinessCourseAllocationController::class);

    Route::resource('purchases', BusinessCoursePurchaseController::class);

    // Reports
    Route::get('/reports', [BusinessReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/purchases', [BusinessReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('/reports/allocations', [BusinessReportController::class, 'allocations'])->name('reports.allocations');
    Route::get('/reports/employee-progress', [BusinessReportController::class, 'employeeProgress'])->name('reports.employee-progress');
    Route::get('/reports/export/{type}', [BusinessReportController::class, 'export'])->name('reports.export');

    Route::resource('courses', BusinessCourseController::class);
});

Route::middleware(['auth', 'business'])->group(function () {
    Route::get('business/certificates/{employee}/{course}/download', [BusinessCertificateController::class, 'download'])
        ->name('business.certificates.download');
});


