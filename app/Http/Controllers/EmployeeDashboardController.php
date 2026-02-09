<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Deposit;
use App\Models\LeaveSubmission;
use App\Models\MonthlySummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeDashboardController extends Controller
{
    /**
     * Show the employee dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get or create monthly summary
        $monthlySummary = MonthlySummary::getOrCreateCurrent($user->id, $currentMonth, $currentYear);

        // Calculate current stats
        $absences = Absence::where('user_id', $user->id)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('type', 'masuk')
            ->get();

        $stats = [
            'hadir' => $absences->where('status', 'hadir')->count(),
            'sakit' => $absences->where('status', 'sakit')->count(),
            'izin' => $absences->where('status', 'izin')->count(),
            'cuti' => LeaveSubmission::getTotalApprovedDaysInMonth($user->id, $currentMonth, $currentYear),
        ];

        // Get deposits for current month
        $depositsData = Deposit::getTotalMonthDeposits($user->id, $currentMonth, $currentYear);

        // Get recent activities
        $recentActivities = [
            'absences' => Absence::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
            'deposits' => Deposit::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
        ];

        // Get unread notifications
        $notifications = \App\Models\Notification::getUnreadForUser($user->id);

        // Get relevant stock info for the role
        $relevantStock = null;
        if ($user->isMiller()) {
            $relevantStock = \App\Models\Stock::getByName('gabah');
        } elseif ($user->isPacking()) {
            $relevantStock = \App\Models\Stock::getByName('beras_giling');
        } elseif ($user->isSales() || $user->isDriver()) {
            $relevantStock = \App\Models\Stock::where('name', 'like', 'packed_%')->get();
        }

        return view('employee.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'depositsData' => $depositsData,
            'recentActivities' => $recentActivities,
            'notifications' => $notifications,
            'relevantStock' => $relevantStock,
        ]);
    }
}
