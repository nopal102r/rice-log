<?php

namespace App\Http\Controllers;

use App\Models\PayrollSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollSettingController extends Controller
{
    /**
     * Show payroll settings page.
     */
    public function index(): View
    {
        $settings = PayrollSetting::getCurrent();
        return view('boss.payroll-settings.index', ['settings' => $settings]);
    }

    /**
     * Update payroll settings.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'price_per_kg' => 'required|numeric|min:100',
            'driver_rate_per_kg' => 'required|numeric|min:0',
            'farmer_rate_per_box' => 'required|numeric|min:0',
            'miller_rate_per_kg' => 'required|numeric|min:0',
            'sales_commission_rate' => 'required|numeric|min:0|max:100',
            'office_latitude' => 'required|numeric|between:-90,90',
            'office_longitude' => 'required|numeric|between:-180,180',
            'max_distance_allowed' => 'required|numeric|min:0.1',
            'leave_days_per_month' => 'required|integer|min:1',
            'min_deposit_per_week' => 'required|integer|min:1',
        ]);

        $settings = PayrollSetting::getCurrent();
        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan gaji berhasil diperbarui.',
            'settings' => $settings,
        ]);
    }
}
