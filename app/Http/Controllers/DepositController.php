<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Deposit;
use App\Models\PayrollSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    /**
     * Show deposit form.
     */
    public function create(): View
    {
        return view('employee.deposit.create', [
            'user' => auth()->user(),
            'settings' => PayrollSetting::getCurrent(),
        ]);
    }

    /**
     * Store a new deposit.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Check if user has checked in today (skip for now if easy debug needed, but keeping for production logic)
        // Checks logic can remain
        if (!Absence::hasCheckedInToday($user->id)) {
             return response()->json([
                'success' => false,
                'message' => 'Anda harus melakukan absen masuk terlebih dahulu sebelum melakukan kegiatan.',
            ], 422);
        }

        $settings = PayrollSetting::getCurrent(); // Always fetched fresh
        $job = $user->job;

        $validated = [];
        $wageAmount = 0;
        $totalPrice = 0; // Context dependent
        $moneyAmount = null; // Sales only
        $boxCount = null;
        $type = 'regular';
        $startTime = null;
        $endTime = null;
        $weight = null;
        $details = null;
        $currentRate = $settings->price_per_kg; // Default

        // --- ROLE BASED VALIDATION & CALCULATION ---

        if ($user->isDriver()) {
            // SUPIR: Gaji dari seberapa banyak (kg) yg ia kirim ke konsumen
            $validated = $request->validate([
                'weight' => 'nullable|numeric|min:0.1',
                'sack_size' => 'nullable|numeric|in:5,10,15,20,25',
                'sack_count' => 'nullable|integer|min:1',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'notes' => 'nullable|string|max:500',
            ]);
            
            if ($validated['sack_size'] && $validated['sack_count']) {
                $weight = $validated['sack_size'] * $validated['sack_count'];
                $details = [['size' => $validated['sack_size'], 'count' => $validated['sack_count']]];
            } else {
                $weight = $validated['weight'] ?? 0;
            }

            // Wage = Weight * Driver Rate
            $currentRate = $settings->driver_rate_per_kg;
            $wageAmount = $weight * $currentRate;
            $totalPrice = $weight * $settings->price_per_kg;

        } elseif ($user->isMiller()) {
            // NGEGILING: Gaji dari berapa kg/karung yg ia giling
            $validated = $request->validate([
                'weight' => 'required|numeric|min:0.1',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'notes' => 'nullable|string|max:500',
            ]);

            $weight = $validated['weight'];
            // Wage = Weight * Miller Rate
            $currentRate = $settings->miller_rate_per_kg;
            $wageAmount = $weight * $currentRate;
            $totalPrice = $weight * $settings->price_per_kg;

        } elseif ($user->isFarmer()) {
            // PETANI: Dua pilihan (Urus Lahan / Setor Beras)
            $type = $request->input('type'); // 'regular' (Setor Beras) or 'land_management' (Urus Lahan)
            
            if ($type === 'land_management') {
                $validated = $request->validate([
                    'type' => 'required|in:land_management',
                    'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                    'start_time' => 'required|date_format:H:i',
                    'end_time' => 'required|date_format:H:i|after:start_time',
                    'box_count' => 'required|integer|min:1',
                    'notes' => 'nullable|string|max:500',
                ]);

                $boxCount = $validated['box_count'];
                $startTime = $validated['start_time'];
                $endTime = $validated['end_time'];
                
                // Wage = Boxes * Farmer Box Rate
                $currentRate = $settings->farmer_rate_per_box;
                $wageAmount = $boxCount * $currentRate;
                $totalPrice = 0; // No rice value for land management

            } else {
                // Setor Beras Mentah / Pare
                $validated = $request->validate([
                    'type' => 'required|in:regular',
                    'weight' => 'required|numeric|min:0.1',
                    'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                    'notes' => 'nullable|string|max:500',
                ]);

                $weight = $validated['weight'];
                // Logic: Petani setor beras -> Get paid for the rice? 
                // Using standard price_per_kg (Buying Price) for the rice value itself. 
                // Or is this just a wage for harvesting? 
                // The prompt says "setor beras mentah... login juga beda bedain".
                // I will assume for now it's treated like a "sale" from farmer to company, so Wage = Weight * PricePerKg.
                // Re-reading: "setor beras mentah" implies giving product. 
                $currentRate = $settings->price_per_kg; 
                $wageAmount = $weight * $currentRate; 
                $totalPrice = $weight * $settings->price_per_kg;
            }

        } elseif ($user->isSales()) {
            // SALES: Setor Uang & Karung Terjual
            // Wage = Setor Uang / Jumlah Karung (per karung, bukan per kg)
            $validated = $request->validate([
                'money_amount' => 'required|numeric|min:0',
                'sack_size' => 'required|numeric|in:5,10,15,20,25', // Updated sizes
                'sack_count' => 'required|integer|min:1', // Jumlah karung
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'notes' => 'nullable|string|max:500',
            ]);

            $moneyAmount = $validated['money_amount'];
            $sackSize = $validated['sack_size'];
            $sackCount = $validated['sack_count'];
            
            // Calculate total weight from sacks (for record keeping)
            $weight = $sackSize * $sackCount;
            
            // Formula: Wage = Money / Sack Count (not weight!)
            $wageAmount = $moneyAmount / $sackCount;
            $totalPrice = $moneyAmount; // Total money brought in

            // Store in details for stock tracking
            $details = [['size' => $sackSize, 'count' => $sackCount]];
        
        } elseif ($user->isPacking()) {
            // PACKING: Setor hasil packing (multi-ukuran)
            $validated = $request->validate([
                'details' => 'required|array|min:1',
                'details.*.size' => 'required|numeric|in:5,10,15,20,25',
                'details.*.count' => 'required|integer|min:1',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'notes' => 'nullable|string|max:500',
            ]);

            $details = $validated['details'];
            $totalWeight = 0;
            $totalSacks = 0;

            foreach ($details as $item) {
                $totalWeight += $item['size'] * $item['count'];
                $totalSacks += $item['count'];
            }

            $weight = $totalWeight;
            // Wage = Total Weight (kg) * Packing Rate per Kg
            $currentRate = $settings->packing_rate_per_kg;
            $wageAmount = $weight * $currentRate;
            $totalPrice = $weight * $settings->price_per_kg;

            // STOCK CHECK: Packing needs 'beras_giling'
            $stock = \App\Models\Stock::getByName('beras_giling');
            if (!$stock || !$stock->isAvailable($weight)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok beras giling tidak mencukupi untuk dipacking. Tersedia: ' . ($stock->quantity ?? 0) . ' kg',
                ], 422);
            }

        } else {
            // Fallback / Generic Employee
             $validated = $request->validate([
                'weight' => 'required|numeric|min:0.1',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'notes' => 'nullable|string|max:500',
            ]);
            $weight = $validated['weight'];
            $wageAmount = 0; // No defined wage logic for generic currently
        }

        // --- GLOBAL STOCK CHECKS FOR OTHER ROLES ---
        if ($user->isMiller()) {
            $stock = \App\Models\Stock::getByName('gabah');
            if (!$stock || !$stock->isAvailable($weight)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok gabah tidak mencukupi untuk digiling. Tersedia: ' . ($stock->quantity ?? 0) . ' kg',
                ], 422);
            }
        } elseif ($user->isSales() || $user->isDriver()) {
            // Check specific sack size stock
            $sackSize = $request->input('sack_size');
            $sackCount = $request->input('sack_count') ?? 1;
            
            if ($sackSize) {
                $stockName = "packed_{$sackSize}kg";
                $stock = \App\Models\Stock::getByName($stockName);
                if (!$stock || !$stock->isAvailable($sackCount)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok karung {$sackSize}kg tidak mencukupi. Tersedia: " . ($stock->quantity ?? 0) . " karung",
                    ], 422);
                }
            }
        }

        // --- STORE DATA ---

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('deposits/' . date('Y/m/d'), 'public');
        }

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'type' => $type,
            'weight' => $weight,
            'box_count' => $boxCount,
            'money_amount' => $moneyAmount,
            'wage_amount' => $wageAmount,
            'price_per_kg' => $currentRate, // Snapshot CORRECT specific rate
            'total_price' => $totalPrice, // Legacy / Contextual
            'start_time' => $startTime,
            'end_time' => $endTime,
            'photo' => $photoPath,
            'notes' => $request->input('notes'),
            'details' => $details ?? null,
            'status' => 'pending', 
        ]);

        // Send notification to boss
        $bosses = \App\Models\User::where('role', 'bos')->get();
        foreach ($bosses as $boss) {
            \App\Models\Notification::create([
                'user_id' => $boss->id,
                'type' => 'deposit_pending',
                'title' => 'Laporan Kerja Baru (' . ucfirst($job ?? 'Karyawan') . ')',
                'message' => $user->name . ' telah mengirim laporan kerja terbaru.',
                'notifiable_type' => Deposit::class,
                'notifiable_id' => $deposit->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dicatat. Menunggu verifikasi bos.',
            'deposit' => $deposit,
            'wage_estimation' => $wageAmount,
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
