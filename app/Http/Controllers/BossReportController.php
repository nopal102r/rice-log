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
     * Show the attendance report page (daily, monthly, yearly).
     */
    public function attendance(Request $request): View
    {
        $period = $request->input('period', 'hari'); // hari, bulan, tahun
        
        $dateStr = $request->input('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($dateStr);
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $employees = User::where('role', 'karyawan')
            ->where(function($query) {
                $query->whereIn('status', ['active', 'aktif', 'Aktif'])
                      ->orWhereNull('status');
            })
            ->get();
        
        $reportData = [];

        if ($period === 'hari') {
            foreach ($employees as $employee) {
                $absences = \App\Models\Absence::where('user_id', $employee->id)
                    ->whereDate('created_at', $date)
                    ->where(function($q) {
                        $q->where('is_manual_req', false)
                          ->orWhere('status_approval', 'approved');
                    })
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
        } elseif ($period === 'bulan' || $period === 'tahun') {
            foreach ($employees as $employee) {
                $query = \App\Models\Absence::where('user_id', $employee->id)
                    ->where('type', 'masuk')
                    ->where(function($q) {
                        $q->where('is_manual_req', false)
                          ->orWhere('status_approval', 'approved');
                    });
                    
                if ($period === 'bulan') {
                    $query->whereMonth('created_at', $month)
                          ->whereYear('created_at', $year);
                } else {
                    $query->whereYear('created_at', $year);
                }
                
                $absences = $query->get();
                
                $reportData[] = (object) [
                    'user' => $employee,
                    'hadir' => $absences->where('status', 'hadir')->count(),
                    'sakit' => $absences->where('status', 'sakit')->count(),
                    'izin' => $absences->where('status', 'izin')->count(),
                ];
            }
        }

        return view('boss.reports.attendance', [
            'reportData' => $reportData,
            'period' => $period,
            'currentDate' => $date,
            'prevDate' => $date->copy()->subDay()->format('Y-m-d'),
            'nextDate' => $date->copy()->addDay()->format('Y-m-d'),
            'month' => $month,
            'year' => $year,
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
            ->where(function($q) {
                $q->where('is_manual_req', false)
                  ->orWhere('status_approval', 'approved');
            })
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

    /**
     * Export the attendance report to CSV.
     */
    public function exportAttendance(Request $request)
    {
        $period = $request->input('period', 'hari'); // hari, bulan, tahun
        
        $dateStr = $request->input('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($dateStr);
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $filename = "laporan_kehadiran_{$period}_";
        if ($period === 'hari') {
            $filename .= $date->format('Y_m_d');
        } elseif ($period === 'bulan') {
            $filename .= "{$year}_{$month}";
        } else {
            $filename .= $year;
        }
        $filename .= '.csv';

        $employees = User::where('role', 'karyawan')
            ->where(function($query) {
                $query->whereIn('status', ['active', 'aktif', 'Aktif'])
                      ->orWhereNull('status');
            })
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        
        $columns = [];
        $rows = [];

        if ($period === 'hari') {
            $columns = ['Nama Karyawan', 'Jabatan', 'Jam Masuk', 'Jam Keluar', 'Status', 'Jarak (Km)'];
            foreach ($employees as $employee) {
                $absences = \App\Models\Absence::where('user_id', $employee->id)
                    ->whereDate('created_at', $date)
                    ->where(function($q) {
                        $q->where('is_manual_req', false)
                          ->orWhere('status_approval', 'approved');
                    })
                    ->get();
                
                $in = $absences->where('type', 'masuk')->first();
                $out = $absences->where('type', 'keluar')->first();
                
                $rows[] = [
                    $employee->name,
                    $employee->job,
                    $in ? $in->created_at->format('H:i') : '-',
                    $out ? $out->created_at->format('H:i') : '-',
                    $in ? $in->status : '-',
                    $in ? $in->distance_from_office : '-',
                ];
            }
        } elseif ($period === 'bulan' || $period === 'tahun') {
            $columns = ['Nama Karyawan', 'Jabatan', 'Hadir', 'Sakit', 'Izin'];
            foreach ($employees as $employee) {
                $query = \App\Models\Absence::where('user_id', $employee->id)
                    ->where('type', 'masuk')
                    ->where(function($q) {
                        $q->where('is_manual_req', false)
                          ->orWhere('status_approval', 'approved');
                    });
                    
                if ($period === 'bulan') {
                    $query->whereMonth('created_at', $month)
                          ->whereYear('created_at', $year);
                } else {
                    $query->whereYear('created_at', $year);
                }
                
                $absences = $query->get();
                
                $rows[] = [
                    $employee->name,
                    $employee->job,
                    $absences->where('status', 'hadir')->count(),
                    $absences->where('status', 'sakit')->count(),
                    $absences->where('status', 'izin')->count(),
                ];
            }
        }

        $callback = function() use($columns, $rows) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
            fputcsv($file, $columns);
            
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export detailed monthly attendance for a specific employee.
     */
    public function exportAttendanceDetail(Request $request, User $user)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $date = \Carbon\Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        
        $attendances = \App\Models\Absence::where('user_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where(function($q) {
                $q->where('is_manual_req', false)
                  ->orWhere('status_approval', 'approved');
            })
            ->get()
            ->groupBy(function($absence) {
                return \Carbon\Carbon::parse($absence->created_at)->format('d');
            });

        $filename = "detail_kehadiran_{$user->name}_{$year}_{$month}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $columns = ['Tanggal', 'Hari', 'Jam Masuk', 'Jam Keluar', 'Status', 'Jarak (Km)'];
        $rows = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
            $dayRecords = $attendances->get($dayStr);
            
            $in = $dayRecords ? $dayRecords->where('type', 'masuk')->first() : null;
            $out = $dayRecords ? $dayRecords->where('type', 'keluar')->first() : null;
            
            $rowDate = \Carbon\Carbon::createFromDate($year, $month, $day);
            
            $rows[] = [
                $rowDate->format('Y-m-d'),
                $rowDate->format('l'),
                $in ? $in->created_at->format('H:i') : '-',
                $out ? $out->created_at->format('H:i') : '-',
                $in ? $in->status : '-',
                $in ? $in->distance_from_office : '-',
            ];
        }

        $callback = function() use($columns, $rows) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
            fputcsv($file, $columns);
            
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
