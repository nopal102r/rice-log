<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlexibilityItem;
use App\Models\UserToken;
use App\Models\PointLedger;
use App\Services\PointLedgerService;

class WalletEmployeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->current_points = $user->getCurrentPoints();
        
        $history = PointLedger::where('user_id', $user->id)->latest()->get();
        $items = FlexibilityItem::all()->map(function($item) {
            if ($item->stock_limit !== null) {
                $used = UserToken::where('item_id', $item->id)->count();
                $item->stock_left = max(0, $item->stock_limit - $used);
            } else {
                $item->stock_left = 'unlimited';
            }
            return $item;
        });
        $inventory = UserToken::with('item')->where('user_id', $user->id)->latest()->get();

        $level = 'Newbie';
        if ($user->current_points > 50) $level = 'Bronze';
        if ($user->current_points > 100) $level = 'Silver';
        if ($user->current_points > 200) $level = 'Gold';
        if ($user->current_points > 500) $level = 'Platinum';

        return view('employee.wallet.dashboard', compact('user', 'history', 'items', 'inventory', 'level'));
    }

    public function purchase(FlexibilityItem $item, PointLedgerService $ledgerService)
    {
        $user = auth()->user();
        $currentPoints = $user->getCurrentPoints();
        
        if ($currentPoints < $item->point_cost) {
            return back()->with('error', 'Poin tidak mencukupi untuk membeli item ini.');
        }

        if ($item->stock_limit !== null) {
            $usedCount = UserToken::where('item_id', $item->id)->count();
            if ($usedCount >= $item->stock_limit) {
                return back()->with('error', 'Stok item ini sudah habis.');
            }
        }

        $ledgerService->recordTransaction($user, 'SPEND', -$item->point_cost, "Pembelian Token: {$item->item_name}");

        UserToken::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status' => 'AVAILABLE',
        ]);

        return back()->with('success', "Berhasil membeli token {$item->item_name}!");
    }
}
