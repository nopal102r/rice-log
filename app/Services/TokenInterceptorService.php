<?php

namespace App\Services;

use App\Models\Absence;

class TokenInterceptorService
{
    /**
     * Attempts to find and use a token to intercept lateness penalty.
     */
    public function interceptLateness(Absence $absence): bool
    {
        $user = $absence->user;
        
        $token = $user->userTokens()
            ->where('status', 'AVAILABLE')
            ->whereHas('item', function($q) {
                // Heuristic: check if item name contains keywords referring to lateness
                $q->where('item_name', 'like', '%telat%')
                  ->orWhere('item_name', 'like', '%late%');
            })->first();

        if ($token) {
            $token->update([
                'status' => 'USED',
                'used_at_absence_id' => $absence->id,
            ]);

            $desc = $absence->description ? $absence->description . ' | ' : '';
            $absence->update([
                'description' => $desc . 'Hadir Tepat Waktu (Token Used) - ' . $token->item->item_name,
            ]);

            return true;
        }

        return false;
    }
}
