<?php

namespace App\Http\Controllers;

use App\Models\LeaveSubmission;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveApprovalController extends Controller
{
    /**
     * Show all pending leave submissions.
     */
    public function index(): View
    {
        $submissions = LeaveSubmission::where('status', 'pending')
            ->with(['user'])
            ->latest()
            ->get();

        $approved = LeaveSubmission::where('status', 'approved')
            ->with(['user', 'approver'])
            ->latest()
            ->limit(10)
            ->get();

        $rejected = LeaveSubmission::where('status', 'rejected')
            ->with(['user', 'approver'])
            ->latest()
            ->limit(10)
            ->get();

        return view('boss.leave-approval.index', [
            'submissions' => $submissions,
            'approved' => $approved,
            'rejected' => $rejected,
        ]);
    }

    /**
     * Approve a leave submission.
     */
    public function approve(LeaveSubmission $submission): JsonResponse
    {
        if ($submission->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Submission sudah diproses sebelumnya.',
            ], 422);
        }

        $submission->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send notification to employee
        Notification::create([
            'user_id' => $submission->user_id,
            'type' => 'leave_approved',
            'title' => 'Pengajuan Cuti Disetujui',
            'message' => 'Pengajuan cuti Anda dari ' . $submission->start_date->format('d-m-Y') .
                ' hingga ' . $submission->end_date->format('d-m-Y') . ' telah disetujui.',
            'notifiable_type' => LeaveSubmission::class,
            'notifiable_id' => $submission->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti disetujui dan notifikasi telah dikirim ke karyawan.',
        ]);
    }

    /**
     * Reject a leave submission.
     */
    public function reject(Request $request, LeaveSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($submission->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Submission sudah diproses sebelumnya.',
            ], 422);
        }

        $submission->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'rejection_reason' => $validated['rejection_reason'],
            'approved_at' => now(),
        ]);

        // Send notification to employee
        Notification::create([
            'user_id' => $submission->user_id,
            'type' => 'leave_rejected',
            'title' => 'Pengajuan Cuti Ditolak',
            'message' => 'Pengajuan cuti Anda telah ditolak. Alasan: ' . $validated['rejection_reason'],
            'notifiable_type' => LeaveSubmission::class,
            'notifiable_id' => $submission->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti ditolak dan notifikasi telah dikirim ke karyawan.',
        ]);
    }
}
