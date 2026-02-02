<?php

namespace App\Http\Controllers;

use App\Models\LeaveSubmission;
use App\Models\Notification;
use App\Models\PayrollSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveSubmissionController extends Controller
{
    /**
     * Show the leave submission form.
     */
    public function create(): View
    {
        return view('employee.leave-submission.create');
    }

    /**
     * Store a new leave submission.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $settings = PayrollSetting::getCurrent();

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        // Calculate total days
        $totalDays = now()->parse($validated['start_date'])->diffInDays(
            now()->parse($validated['end_date'])
        ) + 1;

        // Check if exceeds maximum leave days per month
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $approvedDaysThisMonth = LeaveSubmission::getTotalApprovedDaysInMonth(
            $user->id,
            $currentMonth,
            $currentYear
        );

        $totalApprovedDays = $approvedDaysThisMonth + $totalDays;

        if ($totalApprovedDays > $settings->leave_days_per_month) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan cuti melebihi batas ' . $settings->leave_days_per_month . ' hari per bulan. Anda sudah menggunakan ' . $approvedDaysThisMonth . ' hari.',
            ], 422);
        }

        // Create leave submission
        $submission = LeaveSubmission::create([
            'user_id' => $user->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Send notification to boss
        $bosses = \App\Models\User::where('role', 'bos')->get();

        foreach ($bosses as $boss) {
            Notification::create([
                'user_id' => $boss->id,
                'type' => 'leave_submission',
                'title' => 'Pengajuan Cuti Baru',
                'message' => $user->name . ' mengajukan cuti dari ' .
                    $validated['start_date'] . ' hingga ' . $validated['end_date'],
                'notifiable_type' => LeaveSubmission::class,
                'notifiable_id' => $submission->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti berhasil dikirim ke atasan. Total ' . $totalDays . ' hari.',
            'submission' => $submission,
        ]);
    }

    /**
     * Get leave submissions for current user.
     */
    public function mySubmissions(): View
    {
        $user = auth()->user();
        $submissions = LeaveSubmission::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('employee.leave-submission.my-submissions', [
            'submissions' => $submissions,
        ]);
    }
}
