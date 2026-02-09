<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Deposit;
use App\Models\MonthlySummary;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeManagementController extends Controller
{
    /**
     * Show all employees.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'karyawan');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by active/inactive (presence this month)
        if ($request->filled('activity')) {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            if ($request->activity === 'active') {
                $query->whereHas('monthlySummaries', function ($q) use ($currentMonth, $currentYear) {
                    $q->where('month', $currentMonth)
                        ->where('year', $currentYear)
                        ->where('status', 'active');
                });
            } else {
                $query->whereDoesntHave('monthlySummaries', function ($q) use ($currentMonth, $currentYear) {
                    $q->where('month', $currentMonth)
                        ->where('year', $currentYear)
                        ->where('status', 'active');
                });
            }
        }

        $employees = $query->with('monthlySummaries')->paginate(15);

        // Get current month/year for summaries
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Enhance employee data with current summary
        $employeesData = $employees->map(function ($employee) use ($currentMonth, $currentYear) {
            $summary = MonthlySummary::getOrCreateCurrent($employee->id, $currentMonth, $currentYear);
            $summary->calculateSummary();

            return [
                'employee' => $employee,
                'summary' => $summary,
            ];
        });

        return view('boss.employee-management.index', [
            'employees' => $employees,
            'employeesData' => $employeesData,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Show employee details and performance.
     */
    public function show(User $user): View
    {
        if ($user->role !== 'karyawan') {
            abort(404);
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $summary = MonthlySummary::getOrCreateCurrent($user->id, $currentMonth, $currentYear);
        $summary->calculateSummary();

        // Get last absences
        $recentAbsences = Absence::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get last deposits
        $recentDeposits = Deposit::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get all month data
        $monthlyData = MonthlySummary::where('user_id', $user->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('boss.employee-management.show', [
            'employee' => $user,
            'summary' => $summary,
            'recentAbsences' => $recentAbsences,
            'recentDeposits' => $recentDeposits,
            'monthlyData' => $monthlyData,
        ]);
    }

    /**
     * Create new employee form.
     */
    public function create(): View
    {
        return view('boss.employee-management.create', [
            'jobs' => ['kurir', 'sawah', 'ngegiling'],
        ]);
    }

    /**
     * Store new employee.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string',
            'job' => 'required|in:supir,petani,ngegiling,sales',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
            'face_descriptors' => 'nullable|array',
        ]);

        $employee = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'],
            'job' => $validated['job'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'role' => 'karyawan',
            'status' => 'active',
        ]);

        \Log::info('Creating employee: ' . $employee->id);
        if (!empty($validated['face_descriptors'])) {
            \Log::info('Face descriptors received for employee ' . $employee->id);
        } else {
            \Log::warning('No face descriptors received for employee ' . $employee->id);
        }

        // If face descriptors provided, enroll the face
        if (!empty($validated['face_descriptors'])) {
            $employee->enrollFace($validated['face_descriptors']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Karyawan baru berhasil ditambahkan.' . (empty($validated['face_descriptors']) ? ' Wajah dapat didaftarkan kemudian.' : ' Wajah sudah terdaftar.'),
            'employee' => $employee,
        ]);
    }

    /**
     * Create new boss/manager form.
     */
    public function createBoss(): View
    {
        return view('boss.employee-management.create-boss');
    }

    /**
     * Store new boss/manager.
     */
    public function storeBoss(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
        ]);

        $boss = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
            'role' => 'bos',
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bos/Manajer baru berhasil ditambahkan.',
            'boss' => $boss,
        ]);
    }

    /**
     * Toggle employee status.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        if ($user->role !== 'karyawan') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan karyawan.',
            ], 422);
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Status karyawan berhasil diperbarui menjadi ' . $newStatus,
            'status' => $newStatus,
        ]);
    }
}
