<?php

namespace App\Services;

use App\Models\PointLedger;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointLedgerService
{
    /**
     * Add or deduct points securely.
     */
    public function recordTransaction(User $user, string $type, int $amount, string $description): PointLedger
    {
        return DB::transaction(function () use ($user, $type, $amount, $description) {
            // Lock the latest ledger entry or user row to prevent race conditions
            $latest = PointLedger::where('user_id', $user->id)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $currentBalance = $latest ? $latest->current_balance : 0;
            $newBalance = $currentBalance + $amount;

            return PointLedger::create([
                'user_id' => $user->id,
                'transaction_type' => $type,
                'amount' => $amount,
                'current_balance' => $newBalance,
                'description' => $description,
            ]);
        });
    }

    public function getBalance(User $user): int
    {
        return $user->getCurrentPoints();
    }
}
