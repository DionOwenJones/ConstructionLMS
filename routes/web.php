<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminBusinessController;
use App\Http\Controllers\Admin\AdminAllocationController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Business\BusinessEmployeeController;
use App\Http\Controllers\Business\BusinessReportController;
use App\Http\Controllers\Business\BusinessCourseManagementController;
use App\Http\Controllers\Business\BusinessCertificateController;
use App\Http\Controllers\Business\BusinessSetupController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\CoursePurchaseController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Profile\BusinessUpgradeController;
use App\Http\Controllers\SitePasswordController;
use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Site Password Routes (must be first)
Route::middleware('web')->group(function () {
    Route::get('site-password', [SitePasswordController::class, 'show'])->name('site.password');
    Route::post('check-site-password', [SitePasswordController::class, 'check'])->name('site.password.check');
});

// All other routes should be protected by the password middleware
Route::middleware(['web'])->group(function () {
    // Public routes
    Route::get('/', [HomeController::class, 'index'])->name('welcome');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Course public routes
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}/preview', [CourseController::class, 'preview'])->name('courses.preview');
    Route::get('/courses/{course}/preview/{section}', [CourseController::class, 'previewSection'])->name('courses.preview.section');

    // Certificate routes
    Route::get('/certificates/{id}/download', [CertificateController::class, 'download'])->name('certificates.download')->middleware(['auth', 'verified']);
    Route::get('/courses/{course}/certificate/generate', [CertificateController::class, 'generate'])->name('courses.certificate.generate')->middleware(['auth', 'verified']);

    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register']);
        
        // Password Reset Routes
        Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    // Authenticated routes
    Route::middleware(['auth'])->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        
        // Email Verification Routes
        Route::get('email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->name('verification.verify');
        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send');

        // Protected course routes
        Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/section/{section}', [CourseController::class, 'show'])->name('courses.show.section');
        Route::get('/courses/{course}/sections/{section}', [CourseController::class, 'showSection'])->name('courses.section');
        Route::post('/courses/{course}/complete-section/{section}', [CourseController::class, 'completeSection'])->name('courses.complete.section');
        Route::post('/courses/{course}/complete', [CourseController::class, 'complete'])->name('courses.complete');

        // Dashboard routes
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Course purchase routes
        Route::post('/courses/{course}/purchase', [CoursePurchaseController::class, 'purchase'])->name('courses.purchase');
        Route::get('/courses/{course}/checkout', [CoursePurchaseController::class, 'checkout'])->name('courses.checkout');
        Route::get('/courses/{course}/success', [CoursePurchaseController::class, 'success'])->name('courses.success');
        Route::get('/courses/{course}/cancel', [CoursePurchaseController::class, 'cancel'])->name('courses.cancel');

        // Business routes
        Route::middleware(['auth', 'verified', 'role:business'])->prefix('business')->name('business.')->group(function () {
            Route::get('/', [BusinessController::class, 'dashboard'])->name('dashboard');
            Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [BusinessController::class, 'profile'])->name('profile');
            Route::get('/reports', [BusinessReportController::class, 'index'])->name('reports');
            Route::get('/employees', [BusinessEmployeeController::class, 'index'])->name('employees');
            Route::get('/courses', [BusinessCourseManagementController::class, 'index'])->name('courses');
            Route::get('/certificates', [BusinessCertificateController::class, 'index'])->name('certificates');
            Route::get('/setup', [BusinessSetupController::class, 'index'])->name('setup');
        });

        // Admin routes
        Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
            Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses');
            Route::get('/users', [AdminUserController::class, 'index'])->name('users');
            Route::get('/businesses', [AdminBusinessController::class, 'index'])->name('businesses');
            Route::get('/allocations', [AdminAllocationController::class, 'index'])->name('allocations');
        });
    });

    // Social login routes
    Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
    Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');
});