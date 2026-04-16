<?php

namespace App\Http\Controllers;

use App\Models\PointRule;
use App\Models\FlexibilityItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WalletBossController extends Controller
{
    // --- RULES MANAGEMENT ---
    public function rules(): View
    {
        $rules = PointRule::latest()->get();
        return view('boss.wallet.rules', compact('rules'));
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'target_role' => 'nullable|string|in:karyawan,bos',
            'condition_operator' => 'required|string|in:<,>,BETWEEN,STATUS_EQUALS',
            'condition_value' => 'required|string',
            'point_modifier' => 'required|integer',
        ]);

        PointRule::create($validated);
        return back()->with('success', 'Aturan Poin berhasil ditambahkan.');
    }

    public function updateRule(Request $request, PointRule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'target_role' => 'nullable|string|in:karyawan,bos',
            'condition_operator' => 'required|string|in:<,>,BETWEEN,STATUS_EQUALS',
            'condition_value' => 'required|string',
            'point_modifier' => 'required|integer',
        ]);

        $rule->update($validated);
        return back()->with('success', 'Aturan Poin berhasil diperbarui.');
    }

    public function destroyRule(PointRule $rule): RedirectResponse
    {
        $rule->delete();
        return back()->with('success', 'Aturan Poin berhasil dihapus.');
    }

    // --- CATALOG MANAGEMENT ---
    public function catalog(): View
    {
        $items = FlexibilityItem::with(['userTokens.user'])->latest()->get()->map(function($item) {
            if ($item->stock_limit !== null) {
                $used = $item->userTokens->count();
                $item->stock_left = max(0, $item->stock_limit - $used);
            } else {
                $item->stock_left = 'unlimited';
            }
            return $item;
        });

        return view('boss.wallet.catalog', compact('items'));
    }

    public function storeCatalog(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'point_cost' => 'required|integer|min:0',
            'tolerance_minutes' => 'nullable|integer|min:1',
            'stock_limit' => 'nullable|integer|min:1',
        ]);

        FlexibilityItem::create($validated);
        return back()->with('success', 'Item berhasil ditambahkan ke katalog.');
    }

    public function destroyCatalog(FlexibilityItem $item): RedirectResponse
    {
        $item->delete();
        return back()->with('success', 'Item berhasil dihapus dari katalog.');
    }

    // --- LEADERBOARD ---
    public function leaderboard(Request $request): View
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Get users and their current points through latest ledger
        $users = User::all()->map(function($user) use ($month, $year) {
            $user->current_points = $user->getCurrentPoints();
            
            $monthLedgers = $user->pointLedgers()
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->latest()
                ->get();
            
            $user->monthly_earned = $monthLedgers->where('amount', '>', 0)->sum('amount');
            $user->monthly_spent = abs($monthLedgers->where('transaction_type', 'SPEND')->sum('amount')); 
            $user->monthly_score = $user->monthly_earned; // Base ranking slightly on earned amount for the month
            $user->monthLedgers = $monthLedgers;
            
            return $user;
        })->sortByDesc('monthly_score')->values();

        return view('boss.wallet.leaderboard', compact('users', 'month', 'year'));
    }
}
