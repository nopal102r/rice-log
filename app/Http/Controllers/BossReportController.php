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

    /**
     * Show the daily attendance report page.
     */
    public function attendance(Request $request): View
    {
        $dateStr = $request->input('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($dateStr);

        $employees = User::where('role', 'karyawan')
            ->where(function($query) {
                $query->whereIn('status', ['active', 'aktif', 'Aktif'])
                      ->orWhereNull('status');
            })
            ->get();
        
        $reportData = [];
        foreach ($employees as $employee) {
            $absences = \App\Models\Absence::where('user_id', $employee->id)
                ->whereDate('created_at', $date)
                ->get();
            
            $in = $absences->where('type', 'masuk')->first();
            $out = $absences->where('type', 'keluar')->first();
            
            $reportData[] = (object) [
                'user' => $employee,
                'in' => $in ? $in->created_at->format('H:i') : '-',
                'out' => $out ? $out->created_at->format('H:i') : '-',
                'status' => $in ? $in->status : '-',
                'distance' => $in ? $in->distance_from_office : null,
            ];
        }

        return view('boss.reports.attendance', [
            'reportData' => $reportData,
            'currentDate' => $date,
            'prevDate' => $date->copy()->subDay()->format('Y-m-d'),
            'nextDate' => $date->copy()->addDay()->format('Y-m-d'),
        ]);
    }

    /**
     * Show detailed monthly attendance for a specific employee.
     */
    public function attendanceDetail(Request $request, User $user): View
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $date = \Carbon\Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        
        $attendances = \App\Models\Absence::where('user_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(function($absence) {
                return \Carbon\Carbon::parse($absence->created_at)->format('d');
            });

        $dailyData = [];
        $totalPresent = 0;
        $totalLate = 0; // If you have late logic, but let's stick to basics
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
            $dayRecords = $attendances->get($dayStr);
            
            $in = $dayRecords ? $dayRecords->where('type', 'masuk')->first() : null;
            $out = $dayRecords ? $dayRecords->where('type', 'keluar')->first() : null;
            
            if ($in) $totalPresent++;
            
            $dailyData[$day] = [
                'date' => \Carbon\Carbon::createFromDate($year, $month, $day),
                'in' => $in ? $in->created_at->format('H:i') : '-',
                'out' => $out ? $out->created_at->format('H:i') : '-',
                'status' => $in ? $in->status : '-',
                'distance' => $in ? $in->distance_from_office : '-',
            ];
        }

        return view('boss.reports.attendance-detail', [
            'user' => $user,
            'dailyData' => $dailyData,
            'totalPresent' => $totalPresent,
            'currentMonth' => (int)$month,
            'currentYear' => (int)$year,
        ]);
    }
}
