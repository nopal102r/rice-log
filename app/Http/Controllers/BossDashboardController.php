<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Deposit;
use App\Models\MonthlySummary;
use App\Models\User;
use Illuminate\View\View;

class BossDashboardController extends Controller
{
    /**
     * Show the boss dashboard.
     */
    public function index(): View
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get all employees
        $employees = User::where('role', 'karyawan')->get();

        // Calculate active employees (those with at least one presence this month)
        $activeEmployees = 0;
        // $totalMonthlyIncome = 0; // Legacy
        
        // New Calculations
        // Income = Money Amount from Sales
        $totalMonthlyIncome = Deposit::where('status', 'verified')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('money_amount');

        // Salary Expense = Wage Amount from All
        $totalMonthlySalaryExpense = Deposit::where('status', 'verified')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('wage_amount');

        $employeeSummaries = [];

        foreach ($employees as $employee) {
            $summary = MonthlySummary::getOrCreateCurrent($employee->id, $currentMonth, $currentYear);
            $summary->calculateSummary();

            if ($summary->status === 'active') {
                $activeEmployees++;
                // $totalMonthlyIncome += $summary->total_salary; // Removed legacy summary count
            }

            $employeeSummaries[] = [
                'user' => $employee,
                'summary' => $summary,
            ];
        }

        // Get pending notifications
        $pendingLeaves = \App\Models\LeaveSubmission::where('status', 'pending')
            ->with(['user'])
            ->latest()
            ->get();

        $pendingDeposits = Deposit::where('status', 'pending')
            ->with(['user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('boss.dashboard', [
            'totalEmployees' => $employees->count(),
            'activeEmployees' => $activeEmployees,
            'totalMonthlyIncome' => $totalMonthlyIncome,
            'totalMonthlySalaryExpense' => $totalMonthlySalaryExpense, // Passed new variable
            'pendingLeaves' => $pendingLeaves,
            'pendingDeposits' => $pendingDeposits,
            'employeeSummaries' => $employeeSummaries,
        ]);
    }
}
