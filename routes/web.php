<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Business\BusinessEmployeeController;
use App\Http\Controllers\Business\BusinessReportController;
use App\Http\Controllers\Business\BusinessCourseManagementController;
use App\Http\Controllers\Business\BusinessCertificateController;
use App\Http\Controllers\CertificateController;
use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}/preview', [CourseController::class, 'preview'])->name('courses.preview');

//login + register routes
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Authentication routes
require __DIR__.'/auth.php';

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Course routes
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('{id}/view', [CourseController::class, 'view'])->name('view');
        Route::get('{id}/preview', [CourseController::class, 'preview'])->name('preview');
        Route::post('{id}/enroll', [CourseController::class, 'enroll'])->name('enroll');
        Route::post('{id}/sections/{sectionId}/complete', [CourseController::class, 'completeSection'])->name('complete-section');
        Route::post('{id}/sections/{sectionId}/next', [CourseController::class, 'nextSection'])->name('next-section');
        Route::post('{id}/sections/{sectionId}/previous', [CourseController::class, 'previousSection'])->name('previous-section');
        Route::post('{id}/complete', [CourseController::class, 'completeCourse'])->name('complete');
        Route::post('{id}/sections/{sectionId}/current', [CourseController::class, 'updateCurrentSection'])->name('current-section');
    });

    // Certificate routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('{id}/download', [CertificateController::class, 'download'])->name('download');
    });

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
    });

    // API routes
    Route::get('/api/sections/{section}', [CourseController::class, 'showSection']);
    Route::post('/api/sections/{section}/mark-current', [CourseController::class, 'markCurrentSection']);
});

// Admin routes 
Route::middleware(['auth', CheckRole::class.':'.User::ROLE_ADMIN])->prefix('admin')->name('admin.')->group(function () {
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

    // Users Management 
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
Route::middleware(['auth', CheckRole::class.':'.User::ROLE_BUSINESS])->prefix('business')->name('business.')->group(function () {
    Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');
    Route::get('/certificates', [BusinessController::class, 'certificates'])->name('certificates');
    Route::get('/profile', [BusinessController::class, 'profile'])->name('profile');
    Route::put('/profile', [BusinessController::class, 'update'])->name('profile.update');
    Route::get('/analytics', [BusinessController::class, 'analytics'])->name('analytics');

    // Employee management
    Route::resource('employees', BusinessEmployeeController::class);

    // Course Management
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/available', [BusinessCourseManagementController::class, 'available'])->name('available');
        Route::get('/purchases', [BusinessCourseManagementController::class, 'purchases'])->name('purchases');
        Route::post('/{course}/purchase', [BusinessCourseManagementController::class, 'purchase'])->name('purchase');
    });

    // Certificate routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/employee/{employeeId}', [BusinessCertificateController::class, 'viewEmployeeCertificates'])->name('employee');
        Route::get('/employee/{employeeId}/course/{courseId}/download', [BusinessCertificateController::class, 'download'])->name('download');
    });
});

// Employee/User routes
Route::middleware(['auth', CheckRole::class.':'.User::ROLE_USER])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Course routes
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/{id}', [CourseController::class, 'view'])->name('view');
        Route::get('/{id}/sections/{sectionId}', [CourseController::class, 'showSection'])->name('section');
        Route::post('/{id}/sections/{sectionId}/next', [CourseController::class, 'nextSection'])->name('next-section');
        Route::post('/{id}/sections/{sectionId}/previous', [CourseController::class, 'previousSection'])->name('previous-section');
        Route::post('/{id}/complete', [CourseController::class, 'completeCourse'])->name('complete');
        Route::post('/{id}/sections/{sectionId}/current', [CourseController::class, 'updateCurrentSection'])->name('current-section');
    });

    // Certificate routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/{id}/download', [CertificateController::class, 'download'])->name('download');
    });
});