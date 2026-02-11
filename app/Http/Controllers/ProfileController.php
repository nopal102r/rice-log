<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Deposit;
use App\Models\MonthlySummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user profile.
     */
    public function show(Request $request): View
    {
        $user = auth()->user();
        
        // If user is boss, show boss-specific profile (or shared one with boss details)
        if ($user->role === 'bos') {
            return view('profile.show', [
                'user' => $user,
                'isBoss' => true,
            ]);
        }

        // If user is employee, reuse the rich detail logic
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

        // Calculate job-specific totals for this month
        $monthDeposits = Deposit::where('user_id', $user->id)
            ->where('status', 'verified')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get();

        $jobMetrics = [
            'total_kg' => $monthDeposits->sum('weight'),
            'total_money' => $monthDeposits->sum('money_amount'),
            'total_items' => 0,
            'packing_details' => [],
        ];

        if ($user->job === 'packing') {
            foreach ($monthDeposits as $deposit) {
                if ($deposit->details) {
                    foreach ($deposit->details as $item) {
                        $label = ($item['weight'] ?? 0) . 'kg - ' . ($item['type'] ?? 'Reguler');
                        $jobMetrics['packing_details'][$label] = ($jobMetrics['packing_details'][$label] ?? 0) + ($item['count'] ?? 0);
                        $jobMetrics['total_items'] += ($item['count'] ?? 0);
                    }
                }
            }
        } elseif ($user->job === 'ngegiling' || $user->job === 'petani') {
            $jobMetrics['total_items'] = $monthDeposits->sum('box_count');
        }

        // Get all month data
        $monthlyData = MonthlySummary::where('user_id', $user->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('profile.show', [
            'user' => $user,
            'employee' => $user, // For compatibility with copied templates
            'isBoss' => false,
            'summary' => $summary,
            'recentAbsences' => $recentAbsences,
            'recentDeposits' => $recentDeposits,
            'monthlyData' => $monthlyData,
            'jobMetrics' => $jobMetrics,
        ]);
    }
}
