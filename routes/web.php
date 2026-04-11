<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ClassroomController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EnrollmentController;
use App\Http\Controllers\Web\GradeController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\StudentController;
use App\Http\Controllers\Web\TeacherController;
use App\Http\Controllers\Web\TermController;
use App\Http\Controllers\Web\TurnController;
use App\Http\Controllers\Web\TuitionPlanController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to dashboard
Route::get('/', fn () => redirect()->route('dashboard'));

// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::patch('/students/{student}/study-status', [StudentController::class, 'updateStudyStatus'])->name('students.study-status');

    // Teachers
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');

    // Classrooms
    Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
    Route::post('/classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
    Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update'])->name('classrooms.update');
    Route::get('/classrooms/archived', [ClassroomController::class, 'archiveIndex'])->name('classrooms.archived');
    Route::post('/classrooms/{id}/restore', [ClassroomController::class, 'restore'])->name('classrooms.restore');

    // Grades
    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
    Route::put('/grades/{grade}', [GradeController::class, 'update'])->name('grades.update');

    // Terms
    Route::get('/terms', [TermController::class, 'index'])->name('terms.index');
    Route::post('/terms', [TermController::class, 'store'])->name('terms.store');
    Route::put('/terms/{term}', [TermController::class, 'update'])->name('terms.update');


    // Turns
    Route::get('/turns', [TurnController::class, 'index'])->name('turns.index');
    Route::post('/turns', [TurnController::class, 'store'])->name('turns.store');
    Route::put('/turns/{turn}', [TurnController::class, 'update'])->name('turns.update');
    Route::delete('/turns/{turn}', [TurnController::class, 'destroy'])->name('turns.destroy');

    // Tuition Plans
    Route::get('/tuition-plans', [TuitionPlanController::class, 'index'])->name('tuition-plans.index');
    Route::post('/tuition-plans', [TuitionPlanController::class, 'store'])->name('tuition-plans.store');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/students/{student}', [PaymentController::class, 'showStudentPayments'])->name('payments.students.show');

    // Enrollments
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::post('/enrollments/{enrollment}/upgrade', [EnrollmentController::class, 'upgrade'])->name('enrollments.upgrade');
    Route::post('/enrollments/{enrollment}/transfer', [EnrollmentController::class, 'transfer'])->name('enrollments.transfer');
    Route::get('/enrollments/students/{student}/history', [EnrollmentController::class, 'history'])->name('enrollments.history');
    Route::get('/enrollments/students/{student}/current', [EnrollmentController::class, 'current'])->name('enrollments.current');

    // Reports — Classroom
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/classroom', [ReportController::class, 'classroom'])->name('reports.classroom');
    Route::get('/reports/classroom-summary', [ReportController::class, 'classroomSummary'])->name('reports.classroom.summary');
    Route::get('/reports/classroom-unpaid', [ReportController::class, 'classroomUnpaid'])->name('reports.classroom.unpaid');
    // Reports — Students
    Route::get('/reports/students', [ReportController::class, 'studentSummary'])->name('reports.students');
    Route::get('/reports/students/{student}', [ReportController::class, 'studentProfile'])->name('reports.students.profile');
    // Reports — Term/Grade
    Route::get('/reports/term-grade', [ReportController::class, 'termGradeSummary'])->name('reports.term-grade');
    Route::get('/reports/term-grade/{grade}', [ReportController::class, 'termGradeDetail'])->name('reports.term-grade.detail');
    // Reports — Teachers
    Route::get('/reports/teachers', [ReportController::class, 'teacherSummary'])->name('reports.teachers');
    Route::get('/reports/teachers/{teacher}', [ReportController::class, 'teacherSchedule'])->name('reports.teachers.schedule');
    // Reports — Payments
    Route::get('/reports/payment-revenue', [ReportController::class, 'paymentRevenue'])->name('reports.payment.revenue');
    Route::get('/reports/payment-transactions', [ReportController::class, 'paymentTransactions'])->name('reports.payment.transactions');
    Route::get('/reports/payment-overdue', [ReportController::class, 'paymentOverdue'])->name('reports.payment.overdue');
    Route::get('/reports/payment-receipt/{payment}', [ReportController::class, 'paymentReceipt'])->name('reports.payment.receipt');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/grades-restore', [GradeController::class, 'restoreIndex'])->name('settings.grades.restore');
    Route::get('/settings/teachers-restore', [TeacherController::class, 'restoreIndex'])->name('settings.teachers.restore');
    Route::get('/settings/students-restore', [StudentController::class, 'restoreIndex'])->name('settings.students.restore');
});
