<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BossReportController extends Controller
{
    /**
     * Show the boss report page.
     */
    public function index(Request $request): View
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $employees = User::where('role', 'karyawan')->get();
        $reportData = [];

        foreach ($employees as $employee) {
            $stats = Deposit::getTotalMonthDeposits($employee->id, $month, $year);
            
            $reportData[] = (object) [
                'user' => $employee,
                'total_weight' => $stats['total_kg'],
                'total_revenue' => $stats['total_revenue'],
                'total_wage' => $stats['total_wage'],
                'deposit_count' => $stats['count'],
            ];
        }

        return view('boss.reports.index', [
            'reportData' => $reportData,
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }
}
