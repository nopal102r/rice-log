<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositApprovalController extends Controller
{
    /**
     * Show all deposits.
     */
    public function index(): View
    {
        $pending = Deposit::where('status', 'pending')
            ->with(['user'])
            ->latest()
            ->paginate(15);

        return view('boss.deposit-approval.index', [
            'deposits' => $pending,
        ]);
    }

    /**
     * Verify a deposit.
     */
    public function verify(Deposit $deposit): JsonResponse
    {
        if ($deposit->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Deposit sudah diverifikasi sebelumnya.',
            ], 422);
        }

        $deposit->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Send notification to employee
        Notification::create([
            'user_id' => $deposit->user_id,
            'type' => 'deposit_verified',
            'title' => 'Setor Terverifikasi',
            'message' => 'Setor ' . $deposit->weight . ' kg beras Anda telah diverifikasi. Total: Rp ' .
                number_format($deposit->total_price, 0, ',', '.'),
            'notifiable_type' => Deposit::class,
            'notifiable_id' => $deposit->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Setor diverifikasi.',
        ]);
    }

    /**
     * Reject a deposit.
     */
    public function reject(Request $request, Deposit $deposit): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($deposit->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Deposit sudah diproses sebelumnya.',
            ], 422);
        }

        $deposit->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Send notification to employee
        Notification::create([
            'user_id' => $deposit->user_id,
            'type' => 'deposit_rejected',
            'title' => 'Setor Ditolak',
            'message' => 'Setor Anda ditolak. Alasan: ' . $validated['reason'],
            'notifiable_type' => Deposit::class,
            'notifiable_id' => $deposit->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Setor ditolak.',
        ]);
    }
}
