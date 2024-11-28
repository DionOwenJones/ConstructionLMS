<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminBusinessController;
use App\Http\Controllers\Admin\AdminAllocationController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminDiscountController;
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
Route::get('site-password', [SitePasswordController::class, 'show'])->name('site.password');
Route::post('check-site-password', [SitePasswordController::class, 'check'])->name('site.password.check');

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Course public routes - exclude website password check
Route::withoutMiddleware(\App\Http\Middleware\WebsitePassword::class)->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/section/{section}', [CourseController::class, 'show'])->name('courses.show.section');
    Route::get('/courses/{course}/sections/{section}', [CourseController::class, 'showSection'])->name('courses.section');
    Route::get('/courses/{course}/preview', [CourseController::class, 'preview'])->name('courses.preview');
    Route::get('/courses/{course}/preview/{section}', [CourseController::class, 'previewSection'])->name('courses.preview.section');
});

// Certificate routes
Route::get('/certificates/{id}/download', [CertificateController::class, 'download'])->name('certificates.download')->middleware(['auth', 'verified']);
Route::get('/courses/{course}/certificate/generate', [CertificateController::class, 'generate'])->name('courses.certificate.generate')->middleware(['auth', 'verified']);

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
    try {
        $user = auth()->user();
        if (!$user) {
            return 'Please login first.';
        }

        // Send verification email
        $user->sendEmailVerificationNotification();

        // Also send a test email
        Mail::raw('Test email from Laravel app', function($message) use ($user) {
            $message->to($user->email)
                   ->subject('Test Email');
        });

        return 'Verification and test emails sent successfully! Check your inbox.';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
})->middleware(['auth']);

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
    if (auth()->user()->role === 'business') {
        return view('business.verify-email');
    }
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    // Protected course routes - only for authenticated users
    Route::post('/courses/{course}/sections/{section}/complete', [CourseController::class, 'completeSection'])->name('courses.complete.section');
    Route::post('/courses/{course}/complete', [CourseController::class, 'completeCourse'])->name('courses.complete');
    Route::get('/courses/{course}/certificate', [CourseController::class, 'certificate'])->name('courses.certificate');
    Route::get('/courses/{course}/certificate/download', [CourseController::class, 'downloadCertificate'])->name('courses.certificate.download');
    
    Route::get('/courses/{course}/purchase', [CoursePurchaseController::class, 'show'])->name('courses.purchase');
    Route::post('/courses/{course}/purchase/process', [CoursePurchaseController::class, 'processPurchase'])->name('courses.processPurchase');
    
    // Discount code routes
    Route::post('/discount/validate', [DiscountController::class, 'validateCode'])->name('discount.validate');
    Route::post('/discount/apply', [DiscountController::class, 'applyDiscount'])->name('discount.apply');

    // Certificate routes for users/employees
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/{id}/download', [CertificateController::class, 'download'])->name('download');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Business upgrade routes
    Route::get('/profile/business/upgrade', [BusinessUpgradeController::class, 'upgrade'])->name('profile.upgrade.business');
    Route::get('/profile/business/setup', [BusinessUpgradeController::class, 'setup'])->name('profile.upgrade.business.setup');
    Route::get('/profile/business/legacy', [BusinessUpgradeController::class, 'legacy'])->name('profile.upgrade.business.legacy');
    Route::post('/profile/business/legacy', [BusinessUpgradeController::class, 'storeLegacy'])->name('profile.upgrade.business.legacy.store');
    Route::post('/profile/business/complete', [BusinessUpgradeController::class, 'complete'])->name('profile.upgrade.business.complete');
    Route::post('/profile/business/downgrade', [BusinessUpgradeController::class, 'downgrade'])->name('profile.upgrade.business.downgrade');

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
        // Business Setup Routes
        Route::get('/setup', [BusinessSetupController::class, 'show'])->name('setup')->withoutMiddleware(['verified']);
        Route::post('/setup', [BusinessSetupController::class, 'store'])->name('setup.store')->withoutMiddleware(['verified']);

        // Employee Management Routes
        Route::get('/employees', [BusinessEmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [BusinessEmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [BusinessEmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [BusinessEmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [BusinessEmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [BusinessEmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::post('/employees/{employee}/allocate-course', [BusinessEmployeeController::class, 'allocateCourse'])->name('employees.allocate-course');

        // Course Management Routes
        Route::get('/courses', [BusinessCourseManagementController::class, 'index'])->name('courses.index');
        Route::get('/courses/available', [BusinessCourseManagementController::class, 'available'])->name('courses.available');
        Route::get('/courses/purchased', [BusinessCourseManagementController::class, 'purchased'])->name('courses.purchased');
        Route::get('/courses/purchases', [BusinessCourseManagementController::class, 'purchases'])->name('courses.purchases');
        Route::get('/courses/{course}/purchase', [BusinessCourseManagementController::class, 'purchase'])->name('courses.purchase');
        Route::post('/courses/{course}/purchase/process', [BusinessCoursePurchaseController::class, 'process'])->name('courses.purchase.process');
        Route::get('/courses/{purchase}/allocate', [BusinessCourseManagementController::class, 'allocate'])->name('courses.allocate');
        Route::post('/courses/{purchase}/allocate', [BusinessCourseAllocationController::class, 'store'])->name('courses.allocate.store');

        // Dashboard Routes
        Route::get('/', [BusinessController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');

        // Profile Routes
        Route::get('/profile', [BusinessController::class, 'profile'])->name('profile');
        Route::put('/profile', [BusinessController::class, 'updateProfile'])->name('profile.update');

        // Analytics Route
        Route::get('/analytics', [BusinessController::class, 'analytics'])->name('analytics');

        // Course Management Routes
        Route::get('/courses', [BusinessCourseManagementController::class, 'index'])->name('courses.index');
        Route::get('/courses/available', [BusinessCourseManagementController::class, 'available'])->name('courses.available');
        Route::get('/courses/purchased', [BusinessCourseManagementController::class, 'purchased'])->name('courses.purchased');
        Route::get('/courses/purchases', [BusinessCourseManagementController::class, 'purchases'])->name('courses.purchases');
        Route::get('/courses/{course}', [BusinessCourseManagementController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/allocate', [BusinessCourseManagementController::class, 'allocate'])->name('courses.allocate');
        Route::post('/courses/{course}/allocate', [BusinessCourseManagementController::class, 'storeAllocation'])->name('courses.allocate.store');
        Route::delete('/courses/{course}/deallocate/{user}', [BusinessCourseManagementController::class, 'destroyAllocation'])->name('courses.deallocate');
        Route::post('/courses/{course}/bulk-allocate', [BusinessCourseManagementController::class, 'bulkAllocate'])->name('courses.bulk-allocate');
        Route::get('/courses/{course}/allocations', [BusinessCourseManagementController::class, 'allocations'])->name('courses.allocations');
        Route::get('/courses/{course}/allocations/export', [BusinessCourseManagementController::class, 'exportAllocations'])->name('courses.allocations.export');

        // Course Purchase Routes
        Route::get('/courses/{course}/purchase', [BusinessCourseManagementController::class, 'purchase'])->name('courses.purchase');
        Route::post('/courses/{course}/purchase', [BusinessCourseManagementController::class, 'processPurchase'])->name('courses.purchase.process');
        Route::get('/courses/{course}/purchase/success', [BusinessCourseManagementController::class, 'purchaseSuccess'])->name('courses.purchase.success');
        Route::get('/courses/{course}/purchase/cancel', [BusinessCourseManagementController::class, 'purchaseCancel'])->name('courses.purchase.cancel');

        // Report Routes
        Route::get('/reports', [BusinessReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/employees', [BusinessReportController::class, 'employees'])->name('reports.employees');
        Route::get('/reports/courses', [BusinessReportController::class, 'courses'])->name('reports.courses');
        Route::get('/reports/certificates', [BusinessReportController::class, 'certificates'])->name('reports.certificates');
        Route::get('/reports/export', [BusinessReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/export/employees', [BusinessReportController::class, 'exportEmployees'])->name('reports.export.employees');
        Route::get('/reports/export/courses', [BusinessReportController::class, 'exportCourses'])->name('reports.export.courses');
        Route::get('/reports/export/certificates', [BusinessReportController::class, 'exportCertificates'])->name('reports.export.certificates');
        Route::get('/reports/{report}', [BusinessReportController::class, 'show'])->name('reports.show');

        // Certificate Routes
        Route::get('/certificates', [BusinessCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [BusinessCertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{certificate}/download', [BusinessCertificateController::class, 'download'])->name('certificates.download');
        Route::get('/certificates/export', [BusinessCertificateController::class, 'export'])->name('certificates.export');
    });

    // Admin routes
    Route::group(['middleware' => ['auth', 'role:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        // Dashboard routes
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Profile routes
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');

        // Reports routes (new controller)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/revenue', [ReportsController::class, 'revenue'])->name('revenue');
            Route::get('/users', [ReportsController::class, 'users'])->name('users');
            Route::get('/courses', [ReportsController::class, 'courses'])->name('courses');
            Route::get('/export', [ReportsController::class, 'export'])->name('export');
        });

        // Original report routes
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
        Route::get('/reports/revenue', [AdminReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/users', [AdminReportController::class, 'users'])->name('reports.users');
        Route::get('/reports/courses', [AdminReportController::class, 'courses'])->name('reports.courses');
        Route::get('/reports/export/{type}', [AdminReportController::class, 'export'])->name('reports.export');
        Route::post('/reports/generate', [AdminReportController::class, 'generate'])->name('reports.generate');

        // User management routes
        Route::resource('users', AdminUserController::class);
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/change-role', [AdminUserController::class, 'changeRole'])->name('users.change-role');
        Route::post('/users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');
        Route::post('/users/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
        
        // Course management routes
        Route::resource('courses', AdminCourseController::class);
        Route::get('/courses/{course}/preview', [AdminCourseController::class, 'preview'])->name('courses.preview');
        Route::get('/courses/{course}/manage', [AdminCourseController::class, 'manage'])->name('courses.manage');
        Route::post('/courses/{course}/publish', [AdminCourseController::class, 'publish'])->name('courses.publish');
        Route::post('/courses/{course}/unpublish', [AdminCourseController::class, 'unpublish'])->name('courses.unpublish');
        Route::post('/courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus'])->name('courses.toggle-status');
        Route::post('/courses/{course}/update-order', [AdminCourseController::class, 'updateOrder'])->name('courses.update-order');
        
        // Course content management routes
        Route::post('/courses/{course}/sections', [AdminCourseController::class, 'storeSection'])->name('sections.store');
        Route::put('/courses/sections/{section}', [AdminCourseController::class, 'updateSection'])->name('sections.update');
        Route::delete('/courses/sections/{section}', [AdminCourseController::class, 'destroySection'])->name('sections.destroy');
        Route::post('/courses/sections/{section}/lessons', [AdminCourseController::class, 'storeLesson'])->name('lessons.store');
        Route::put('/courses/lessons/{lesson}', [AdminCourseController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/courses/lessons/{lesson}', [AdminCourseController::class, 'destroyLesson'])->name('lessons.destroy');
        Route::post('/courses/lessons/reorder', [AdminCourseController::class, 'reorderLessons'])->name('lessons.reorder');
        
        // Business management routes
        Route::resource('businesses', AdminBusinessController::class);
        Route::get('/businesses/{business}/employees', [AdminBusinessController::class, 'employees'])->name('businesses.employees');
        Route::get('/businesses/{business}/courses', [AdminBusinessController::class, 'courses'])->name('businesses.courses');
        Route::post('/businesses/{business}/allocate-courses', [AdminBusinessController::class, 'allocateCourses'])->name('businesses.allocate-courses');
        Route::delete('/businesses/{business}/remove-course/{course}', [AdminBusinessController::class, 'removeCourse'])->name('businesses.remove-course');
        
        // Course allocation routes
        Route::get('/allocations', [AdminAllocationController::class, 'index'])->name('allocations.index');
        Route::post('/allocations/store', [AdminAllocationController::class, 'store'])->name('allocations.store');
        Route::delete('/allocations/{allocation}', [AdminAllocationController::class, 'destroy'])->name('allocations.destroy');
        
        // Search routes
        Route::get('/search', [AdminSearchController::class, 'search'])->name('search');
        Route::get('/search/results', [AdminSearchController::class, 'results'])->name('search.results');

        // Discount routes
        Route::resource('discounts', AdminDiscountController::class);
        Route::post('discounts/{discount}/toggle-status', [AdminDiscountController::class, 'toggleStatus'])->name('discounts.toggle-status');

        // Assessment routes
        Route::resource('courses.assessments', AssessmentController::class);

        // Assessment Questions
        Route::get('/courses/{course}/assessments/{assessment}/questions', [AssessmentQuestionController::class, 'index'])->name('courses.assessments.questions.index');
        Route::get('/courses/{course}/assessments/{assessment}/questions/create', [AssessmentQuestionController::class, 'create'])->name('courses.assessments.questions.create');
        Route::post('/courses/{course}/assessments/{assessment}/questions', [AssessmentQuestionController::class, 'store'])->name('courses.assessments.questions.store');
        Route::get('/courses/{course}/assessments/{assessment}/questions/{question}/edit', [AssessmentQuestionController::class, 'edit'])->name('courses.assessments.questions.edit');
        Route::put('/courses/{course}/assessments/{assessment}/questions/{question}', [AssessmentQuestionController::class, 'update'])->name('courses.assessments.questions.update');
        Route::delete('/courses/{course}/assessments/{assessment}/questions/{question}', [AssessmentQuestionController::class, 'destroy'])->name('courses.assessments.questions.destroy');

        // Discount routes
    });

    Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
        ->name('socialite.redirect');
    Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback'])
        ->name('socialite.callback');
});