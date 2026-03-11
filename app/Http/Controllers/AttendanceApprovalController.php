<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceApprovalController extends Controller
{
    /**
     * Display a listing of pending manual attendance requests.
     */
    public function index(): View
    {
        $pendingAttendances = Absence::where('is_manual_req', true)
            ->where('status_approval', 'pending')
            ->with(['user'])
            ->latest()
            ->get();

        $historyAttendances = Absence::where('is_manual_req', true)
            ->where('status_approval', '!=', 'pending')
            ->with(['user'])
            ->latest()
            ->limit(50)
            ->get();

        return view('boss.attendance-approval.index', compact('pendingAttendances', 'historyAttendances'));
    }

    /**
     * Approve the manual attendance request.
     */
    public function approve(Absence $absence)
    {
        $absence->update([
            'status_approval' => 'approved'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi manual berhasil disetujui.'
        ]);
    }

    /**
     * Reject the manual attendance request.
     */
    public function reject(Absence $absence)
    {
        $absence->update([
            'status_approval' => 'rejected'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi manual telah ditolak.'
        ]);
    }
}
