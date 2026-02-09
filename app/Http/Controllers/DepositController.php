<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Deposit;
use App\Models\PayrollSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositController extends Controller
{
    /**
     * Show deposit form.
     */
    public function create(): View
    {
        return view('employee.deposit.create');
    }

    /**
     * Store a new deposit.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Check if user has checked in today
        if (!Absence::hasCheckedInToday($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus melakukan absen masuk terlebih dahulu sebelum melakukan setor.',
            ], 422);
        }

        $validated = $request->validate([
            'weight' => 'required|numeric|min:0.1',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $settings = PayrollSetting::getCurrent();

        // Store photo
        $photoPath = $request->file('photo')->store('deposits/' . date('Y/m/d'), 'public');
        
        $pricePerKg = $settings->price_per_kg;
        $totalPrice = $validated['weight'] * $pricePerKg;

        // Create deposit
        $deposit = Deposit::create([
            'user_id' => $user->id,
            'weight' => $validated['weight'],
            'price_per_kg' => $pricePerKg,
            'total_price' => $totalPrice,
            'photo' => $photoPath,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending', // Admin/boss akan verify
        ]);

        // Send notification to boss
        $bosses = \App\Models\User::where('role', 'bos')->get();

        foreach ($bosses as $boss) {
            \App\Models\Notification::create([
                'user_id' => $boss->id,
                'type' => 'deposit_pending',
                'title' => 'Setor Beras Menunggu Verifikasi',
                'message' => $user->name . ' telah melakukan setor ' . $validated['weight'] . ' kg beras.',
                'notifiable_type' => Deposit::class,
                'notifiable_id' => $deposit->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Setor berhasil dicatat. Menunggu verifikasi dari atasan.',
            'deposit' => $deposit,
        ]);
    }

    /**
     * Get my deposits.
     */
    public function myDeposits(): View
    {
        $user = auth()->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $deposits = Deposit::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $depositsData = Deposit::getTotalMonthDeposits($user->id, $currentMonth, $currentYear);

        return view('employee.deposit.my-deposits', [
            'deposits' => $deposits,
            'depositsData' => $depositsData,
        ]);
    }
}
