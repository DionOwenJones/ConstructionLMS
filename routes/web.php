<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminUserController;
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
use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Course public routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}/preview', [CourseController::class, 'preview'])->name('courses.preview');

// Certificate routes
Route::get('/certificates/{id}/download', [CertificateController::class, 'download'])->name('certificates.download')->middleware(['auth', 'verified']);

// Debug routes (remove in production)
Route::get('/debug-email', [TestEmailController::class, 'testEmail']);

// Test certificate route (remove in production)
Route::get('/test-certificate/{course}', function(\App\Models\Course $course) {
    $user = auth()->user();
    if (!$user) {
        return 'Please login first.';
    }

    // Mark course as completed for testing
    DB::table('course_user')
        ->where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->update([
            'completed' => true,
            'completed_at' => now()
        ]);

    // Generate certificate
    $certificate = \App\Models\Certificate::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'certificate_number' => sprintf('CERT-%s-%s-%s', 
            strtoupper(substr($user->name, 0, 3)),
            $course->id,
            now()->format('Ymd')
        ),
        'issued_at' => now()
    ]);

    return redirect()->route('certificates.download', $certificate->id);
})->middleware(['auth', 'verified']);

// Test email route (remove in production)
Route::get('/test-email', function() {
    $user = auth()->user();
    if ($user) {
        $user->sendEmailVerificationNotification();
        return 'Verification email sent!';
    }
    return 'Please login first.';
});

//login + register routes
Route::middleware('guest')->group(function () {
    // Authentication Routes
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

Route::post('logout', [LoginController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    // Protected course routes
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/section/{section?}', [CourseController::class, 'show'])->name('courses.show.section');
    Route::post('/courses/{course}/section/{section}/complete', [CourseController::class, 'completeSection'])->name('courses.section.complete');
    Route::post('/courses/{course}/complete', [CourseController::class, 'completeCourse'])->name('courses.complete');
    Route::get('/courses/{course}/certificate', [CourseController::class, 'certificate'])->name('courses.certificate');

    // Certificate routes for users/employees
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/{id}/download', [CertificateController::class, 'download'])->name('download');
    });

    // Course purchase routes
    Route::middleware(['verified'])->group(function () {
        Route::get('/courses/{course}/purchase', [CoursePurchaseController::class, 'show'])->name('courses.purchase');
        Route::post('/courses/{course}/purchase/process', [CoursePurchaseController::class, 'purchase'])
            ->name('courses.purchase.process')
            ->middleware('web');
    });

    // Profile routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/profile/upgrade-to-business', [BusinessUpgradeController::class, 'upgrade'])->name('profile.upgrade.business');
    });

    // Dashboard route - only for non-business users
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified', 'role:user'])
        ->name('dashboard');

    // Business setup routes (no role middleware)
    Route::middleware(['auth', 'verified'])->prefix('business')->name('business.')->group(function () {
        Route::get('/setup', [BusinessSetupController::class, 'show'])->name('setup');
        Route::post('/setup', [BusinessSetupController::class, 'store'])->name('setup.store');
    });

    // Business routes
    Route::middleware(['auth', 'verified', 'role:business'])->prefix('business')->name('business.')->group(function () {
        // Dashboard and main routes
        Route::get('/', [BusinessController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [BusinessController::class, 'profile'])->name('profile');
        Route::put('/profile', [BusinessController::class, 'updateProfile'])->name('profile.update');
        Route::get('/analytics', [BusinessController::class, 'analytics'])->name('analytics');

        // Employee management
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [BusinessEmployeeController::class, 'index'])->name('index');
            Route::get('/create', [BusinessEmployeeController::class, 'create'])->name('create');
            Route::post('/', [BusinessEmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}', [BusinessEmployeeController::class, 'show'])->name('show');
            Route::get('/{employee}/edit', [BusinessEmployeeController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [BusinessEmployeeController::class, 'update'])->name('update');
            Route::delete('/{employee}', [BusinessEmployeeController::class, 'destroy'])->name('destroy');
        });
        
        // Course management
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [BusinessCourseManagementController::class, 'index'])->name('index');
            Route::get('/available', [BusinessCourseManagementController::class, 'available'])->name('available');
            Route::get('/purchases', [BusinessCourseManagementController::class, 'purchases'])->name('purchases');
            Route::get('/{course}/purchase', [BusinessCourseManagementController::class, 'showPurchaseForm'])->name('purchase');
            Route::post('/{course}/purchase', [BusinessCourseManagementController::class, 'purchaseCourse'])->name('purchase.process');
            Route::get('/purchase/{purchase}/allocate', [BusinessCourseManagementController::class, 'showAllocationForm'])->name('allocate');
            Route::post('/purchase/{purchase}/allocate', [BusinessCourseManagementController::class, 'allocate'])->name('allocate.process');
        });

        // Certificate management
        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [BusinessCertificateController::class, 'index'])->name('index');
            Route::get('/employee/{employeeId}', [BusinessCertificateController::class, 'viewEmployeeCertificates'])->name('employee');
            Route::get('/employee/{employeeId}/course/{courseId}/download', [BusinessCertificateController::class, 'download'])->name('download');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [BusinessReportController::class, 'index'])->name('index');
            Route::get('/progress', [BusinessReportController::class, 'progress'])->name('progress');
            Route::get('/completion', [BusinessReportController::class, 'completion'])->name('completion');
            Route::get('/engagement', [BusinessReportController::class, 'engagement'])->name('engagement');
            Route::get('/export/{type?}', [BusinessReportController::class, 'export'])->name('export');
        });
    });
});

// Social login routes
Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

// Admin routes 
Route::middleware(['auth', 'verified', CheckRole::class.':'.User::ROLE_ADMIN])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Admin course management
    Route::resource('courses', AdminCourseController::class);
    Route::post('/courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::post('/courses/{course}/sections/reorder', [AdminCourseController::class, 'reorderSections'])->name('courses.sections.reorder');
    Route::post('/courses/{course}/sections', [AdminCourseController::class, 'storeSection'])->name('courses.sections.store');
    Route::put('/courses/{course}/sections/{section}', [AdminCourseController::class, 'updateSection'])->name('courses.sections.update');
    Route::delete('/courses/{course}/sections/{section}', [AdminCourseController::class, 'destroySection'])->name('courses.sections.destroy');
    
    // Admin user management
    Route::resource('users', AdminUserController::class);
});