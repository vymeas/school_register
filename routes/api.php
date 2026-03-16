<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\TermController;
use App\Http\Controllers\Api\TuitionPlanController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');

    // Students
    Route::apiResource('students', StudentController::class)->names('api.students');

    // Classrooms
    Route::apiResource('classrooms', ClassroomController::class)->names('api.classrooms');

    // Grades
    Route::apiResource('grades', GradeController::class)->names('api.grades');
    Route::put('/grades/{grade}/restore', [GradeController::class, 'restore'])->name('api.grades.restore');

    // Terms
    Route::apiResource('terms', TermController::class)->names('api.terms');

    // Teachers
    Route::apiResource('teachers', TeacherController::class)->names('api.teachers');
    Route::put('/teachers/{teacher}/restore', [TeacherController::class, 'restore'])->name('api.teachers.restore');

    // Tuition Plans
    Route::apiResource('tuition-plans', TuitionPlanController::class)->names('api.tuition-plans');

    // Payments
    Route::apiResource('payments', PaymentController::class)->only(['index', 'store', 'show'])->names('api.payments');

    // Enrollments
    Route::apiResource('enrollments', EnrollmentController::class)->only(['index', 'store', 'show', 'update'])->names('api.enrollments');

    // Reports
    Route::get('/reports/payments', [ReportController::class, 'paymentReport'])->name('api.reports.payments');
    Route::get('/reports/students', [ReportController::class, 'studentReport'])->name('api.reports.students');

    // Users (Admin only)
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::apiResource('users', UserController::class)->names('api.users');
    });
});
