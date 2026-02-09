<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BossDashboardController;
use App\Http\Controllers\DepositApprovalController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\EmployeeManagementController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveSubmissionController;
use App\Http\Controllers\PayrollSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Employee Routes
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Absences
    Route::get('/absence/{type}', [AbsenceController::class, 'show'])->name('absence.form')->where('type', 'masuk|keluar');
    Route::post('/absence', [AbsenceController::class, 'store'])->name('absence.store');

    // Leave Submissions
    Route::get('/leave/create', [LeaveSubmissionController::class, 'create'])->name('leave.create');
    Route::post('/leave', [LeaveSubmissionController::class, 'store'])->name('leave.store');
    Route::get('/leave/my-submissions', [LeaveSubmissionController::class, 'mySubmissions'])->name('leave.my-submissions');

    // Deposits
    Route::get('/deposit/create', [DepositController::class, 'create'])->name('deposit.create');
    Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');
    Route::get('/deposit/my-deposits', [DepositController::class, 'myDeposits'])->name('deposit.my-deposits');
});

// Boss Routes
Route::middleware(['auth', 'boss'])->prefix('boss')->name('boss.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [BossDashboardController::class, 'index'])->name('dashboard');

    // Employee Management
    Route::resource('employees', EmployeeManagementController::class);
    Route::patch('/employees/{employee}/toggle-status', [\App\Http\Controllers\EmployeeManagementController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::get('/boss-management/create', [EmployeeManagementController::class, 'createBoss'])->name('boss-management.create');
    Route::post('/boss-management', [EmployeeManagementController::class, 'storeBoss'])->name('boss-management.store');

    // Leave Approval
    Route::get('/leave-approval', [LeaveApprovalController::class, 'index'])->name('leave-approval.index');
    Route::post('/leave-approval/{submission}/approve', [LeaveApprovalController::class, 'approve'])->name('leave-approval.approve');
    Route::post('/leave-approval/{submission}/reject', [LeaveApprovalController::class, 'reject'])->name('leave-approval.reject');

    // Deposit Approval
    Route::get('/deposit-approval', [DepositApprovalController::class, 'index'])->name('deposit-approval.index');
    Route::post('/deposit-approval/{deposit}/verify', [DepositApprovalController::class, 'verify'])->name('deposit-approval.verify');
    Route::post('/deposit-approval/{deposit}/reject', [DepositApprovalController::class, 'reject'])->name('deposit-approval.reject');

    // Payroll Settings
    Route::get('/payroll-settings', [PayrollSettingController::class, 'index'])->name('payroll-settings.index');
    Route::post('/payroll-settings', [PayrollSettingController::class, 'update'])->name('payroll-settings.update');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\BossReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/attendance', [\App\Http\Controllers\BossReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/attendance/detail/{user}', [\App\Http\Controllers\BossReportController::class, 'attendanceDetail'])->name('reports.attendance.detail');

    // Stock Inventory
    Route::get('/stock', [\App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
});

// Authenticated Routes (both roles)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isBoss()) {
            return redirect()->route('boss.dashboard');
        }
        return redirect()->route('employee.dashboard');
    })->name('dashboard');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
});

