<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LandingContentController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class)->name('landing');

Route::view('/demo-dashboard', 'demo.dashboard')->name('demo.dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware(['auth', 'active', 'password.changed'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/classes', [AdminController::class, 'classDirectory'])
        ->middleware('permission:school.manage,assessments.manage')
        ->name('classes.index');
    Route::get('/classes/subjects', [AdminController::class, 'classDirectory'])->defaults('section', 'subjects')
        ->middleware('permission:school.manage')->name('classes.subjects');
    Route::get('/classes/teaching', [AdminController::class, 'classDirectory'])->defaults('section', 'teaching')
        ->middleware('permission:school.manage')->name('classes.teaching');
    Route::get('/classes/promotions', [AdminController::class, 'classDirectory'])->defaults('section', 'promotions')
        ->middleware('permission:school.manage')->name('classes.promotions');
    Route::get('/classes/{schoolClass}', [AdminController::class, 'showClass'])
        ->middleware('permission:school.manage,assessments.manage')->name('classes.show');
    Route::post('/classes/{schoolClass}/attendance', [AdminController::class, 'recordClassAttendance'])
        ->middleware('permission:school.manage,assessments.manage')->name('classes.attendance.store');
    Route::get('/students', [AdminController::class, 'students'])
        ->middleware('permission:school.manage,assessments.manage')->name('students.index');
    Route::get('/students/{student}', [AdminController::class, 'showStudent'])
        ->middleware('permission:school.manage,assessments.manage')->name('students.show');
    Route::post('/classes/{schoolClass}/promote', [AdminController::class, 'promoteStudents'])
        ->middleware('permission:school.manage')->name('classes.promote');

    Route::middleware('role:superadmin')->group(function () {
        Route::get('/admin/landing', [LandingContentController::class, 'index'])->name('admin.landing.index');
        Route::get('/admin/landing/hero', [LandingContentController::class, 'section'])->defaults('section', 'hero')->name('admin.landing.hero');
        Route::get('/admin/landing/profile', [LandingContentController::class, 'section'])->defaults('section', 'profile')->name('admin.landing.profile');
        Route::get('/admin/landing/admission', [LandingContentController::class, 'section'])->defaults('section', 'admission')->name('admin.landing.admission');
        Route::get('/admin/landing/content/{kind}', [LandingContentController::class, 'content'])->name('admin.landing.content');
        Route::get('/admin/landing/tuition', [LandingContentController::class, 'tuition'])->name('admin.landing.tuition');
        Route::put('/admin/landing/sections/{section}', [LandingContentController::class, 'updateSection'])->name('admin.landing.sections.update');
        Route::put('/admin/landing', [LandingContentController::class, 'updatePage'])->name('admin.landing.update');
        Route::post('/admin/landing/items', [LandingContentController::class, 'storeItem'])->name('admin.landing.items.store');
        Route::put('/admin/landing/items/{landingItem}', [LandingContentController::class, 'updateItem'])->name('admin.landing.items.update');
        Route::delete('/admin/landing/items/{landingItem}', [LandingContentController::class, 'destroyItem'])->name('admin.landing.items.destroy');
        Route::post('/admin/landing/tuition-packages', [LandingContentController::class, 'storeTuitionPackage'])->name('admin.landing.tuition.store');
        Route::put('/admin/landing/tuition-packages/{tuitionPackage}', [LandingContentController::class, 'updateTuitionPackage'])->name('admin.landing.tuition.update');
        Route::delete('/admin/landing/tuition-packages/{tuitionPackage}', [LandingContentController::class, 'destroyTuitionPackage'])->name('admin.landing.tuition.destroy');
    });

    Route::middleware('permission:users.manage')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/users/create', [AdminController::class, 'users'])->defaults('section', 'create')->name('admin.users.create');
        Route::get('/admin/users/import', [AdminController::class, 'users'])->defaults('section', 'import')->name('admin.users.import');
        Route::get('/admin/users/roles', [AdminController::class, 'users'])->defaults('section', 'roles')->name('admin.users.roles');
        Route::get('/admin/users/parents', [AdminController::class, 'users'])->defaults('section', 'parents')->name('admin.users.parents');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::post('/admin/users/import-google-sheet', [AdminController::class, 'importGoogleSheet'])->name('admin.users.import-google-sheet');
        Route::post('/admin/users/import-spreadsheet', [AdminController::class, 'importSpreadsheet'])->name('admin.users.import-spreadsheet');
        Route::get('/admin/users/import-template/{role}', [AdminController::class, 'downloadImportTemplate'])->whereNumber('role')->name('admin.users.import-template');
        Route::post('/admin/users/{user}/role', [AdminController::class, 'assignRole'])->name('admin.users.role');
        Route::post('/admin/roles', [AdminController::class, 'storeRole'])->name('admin.roles.store');
        Route::post('/admin/roles/{role}', [AdminController::class, 'updateRole'])->name('admin.roles.update');
        Route::post('/admin/users/link-parent', [AdminController::class, 'linkParent'])->name('admin.users.link-parent');
    });

    Route::middleware('permission:school.manage')->group(function () {
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::get('/admin/settings/classes', [AdminController::class, 'settings'])->defaults('section', 'classes')->name('admin.settings.classes');
        Route::get('/admin/settings/academic', [AdminController::class, 'settings'])->defaults('section', 'academic')->name('admin.settings.academic');
        Route::get('/admin/settings/iot', [AdminController::class, 'settings'])->defaults('section', 'iot')->name('admin.settings.iot');
        Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
        Route::post('/admin/classes', [AdminController::class, 'storeClass'])->name('admin.classes.store');
        Route::post('/admin/academic-years', [AdminController::class, 'storeAcademicYear'])->name('admin.academic-years.store');
        Route::post('/admin/semesters', [AdminController::class, 'storeSemester'])->name('admin.semesters.store');
        Route::post('/admin/classes/{schoolClass}/homeroom', [AdminController::class, 'setHomeroomTeacher'])->name('admin.classes.homeroom');
        Route::post('/admin/iot-devices', [AdminController::class, 'storeDevice'])->name('admin.iot-devices.store');
        Route::post('/admin/subjects', [AdminController::class, 'storeSubject'])->name('admin.subjects.store');
        Route::post('/admin/teaching-assignments', [AdminController::class, 'storeTeachingAssignment'])->name('admin.teaching-assignments.store');
        Route::delete('/admin/teaching-assignments/{teachingAssignment}', [AdminController::class, 'destroyTeachingAssignment'])->name('admin.teaching-assignments.destroy');
        Route::post('/classes/students/{student}/promote', [FinanceController::class, 'promoteStudent'])->name('classes.students.promote');
    });

    Route::middleware('permission:attendance.checkin')->group(function () {
        Route::post('/attendance/check-in', [AttendanceController::class, 'store'])->middleware('throttle:10,1')->name('attendance.check-in');
    });

    Route::middleware('permission:attendance.manage')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/today', [AttendanceController::class, 'index'])->defaults('scope', 'today')->name('attendance.today');
    });

    Route::middleware('permission:assessments.manage')->group(function () {
        Route::get('/teacher/grades', [GradeController::class, 'index'])->name('teacher.grades.index');
        Route::get('/teacher/grades/create', [GradeController::class, 'index'])->defaults('section', 'create')->name('teacher.grades.create');
        Route::post('/teacher/grades', [GradeController::class, 'store'])->name('teacher.grades.store');
        Route::get('/teacher/grades/{assessment}', [GradeController::class, 'show'])->name('teacher.grades.show');
        Route::post('/teacher/grades/{assessment}', [GradeController::class, 'save'])->name('teacher.grades.save');
        Route::get('/teacher/assessments', [AssessmentController::class, 'create'])->name('teacher.assessments');
        Route::get('/teacher/assessments/achievements', [AssessmentController::class, 'create'])->defaults('section', 'achievement')->name('teacher.assessments.achievements');
        Route::post('/teacher/assessments/attitude', [AssessmentController::class, 'storeAttitude'])->name('teacher.assessments.attitude');
        Route::post('/teacher/assessments/achievement', [AssessmentController::class, 'storeAchievement'])->name('teacher.assessments.achievement');
    });

    Route::middleware('permission:reports.export')->group(function () {
        Route::get('/reports/attendance.csv', [ReportController::class, 'attendanceCsv'])->name('reports.attendance');
        Route::get('/reports/attitude.csv', [ReportController::class, 'attitudeCsv'])->name('reports.attitude');
    });

    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/payments', [FinanceController::class, 'index'])->defaults('section', 'payments')->name('finance.payment-history');
    Route::get('/finance/payments/{payment}/proof', [FinanceController::class, 'paymentProof'])->name('finance.payments.proof');
    Route::post('/finance/bills/{bill}/confirm-payment', [FinanceController::class, 'submitPaymentConfirmation'])->name('finance.payments.confirm');
    Route::get('/finance/issue', [FinanceController::class, 'index'])->defaults('section', 'issue')->middleware('permission:finance.manage')->name('finance.issue');
    Route::get('/finance/fee-types', [FinanceController::class, 'index'])->defaults('section', 'fee-types')->middleware('permission:finance.manage')->name('finance.fee-types');
    Route::get('/finance/proposals', [FinanceController::class, 'index'])->defaults('section', 'proposals')->middleware('permission:finance.manage,finance.propose')->name('finance.proposals');
    Route::get('/finance/promotions', fn () => redirect()->route('classes.promotions'))->middleware('permission:school.manage')->name('finance.promotions');
    Route::get('/finance/record-payment', [FinanceController::class, 'index'])->defaults('section', 'record-payment')->middleware('permission:finance.manage')->name('finance.record-payment');
    Route::get('/finance/payment-confirmations', [FinanceController::class, 'index'])->defaults('section', 'confirmations')->middleware('permission:finance.manage')->name('finance.confirmations');

    Route::middleware('permission:finance.propose')->group(function () {
        Route::post('/finance/proposals', [FinanceController::class, 'storeProposal'])->name('finance.proposals.store');
    });

    Route::middleware('permission:finance.manage')->group(function () {
        Route::post('/finance/fee-types', [FinanceController::class, 'storeFeeType'])->name('finance.fee-types.store');
        Route::post('/finance/bills/issue', [FinanceController::class, 'issueBills'])->name('finance.bills.issue');
        Route::post('/finance/proposals/{proposal}/approve', [FinanceController::class, 'approveProposal'])->name('finance.proposals.approve');
        Route::post('/finance/proposals/{proposal}/reject', [FinanceController::class, 'rejectProposal'])->name('finance.proposals.reject');
        Route::post('/finance/bills/{bill}/payments', [FinanceController::class, 'storePayment'])->name('finance.payments.store');
        Route::post('/finance/payments/{payment}/approve', [FinanceController::class, 'approvePayment'])->name('finance.payments.approve');
        Route::post('/finance/payments/{payment}/reject', [FinanceController::class, 'rejectPayment'])->name('finance.payments.reject');
    });
});
