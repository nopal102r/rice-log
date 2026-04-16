<?php

namespace App\Services;

use App\Models\Absence;

class TokenInterceptorService
{
    /**
     * Attempts to find and use a token to intercept lateness penalty.
     * Returns an array with 'intercepted' boolean and 'message' string.
     */
    public function interceptLateness(Absence $absence, \App\Models\PointRule $rule): array
    {
        $user = $absence->user;
        
        // Calculate late minutes
        $checkTime = \Carbon\Carbon::createFromFormat('H:i', $absence->created_at->timezone('Asia/Jakarta')->format('H:i'));
        $ruleTime = \Carbon\Carbon::createFromFormat('H:i', $rule->condition_value);
        $lateMinutes = (int) $ruleTime->diffInMinutes($checkTime, false);
        
        if ($lateMinutes <= 0) {
            return [
                'intercepted' => false,
                'message' => "Automated Rule: {$rule->rule_name}"
            ];
        }

        $availableTokens = $user->userTokens()
            ->with('item')
            ->where('status', 'AVAILABLE')
            ->whereHas('item', function($q) {
                $q->whereNotNull('tolerance_minutes')->where('tolerance_minutes', '>', 0);
            })->get();

        $suitableToken = null;
        foreach ($availableTokens as $token) {
            if ($token->item->tolerance_minutes >= $lateMinutes) {
                $suitableToken = $token;
                break;
            }
        }

        if ($suitableToken) {
            $suitableToken->update([
                'status' => 'USED',
                'used_at_absence_id' => $absence->id,
            ]);

            $desc = $absence->description ? $absence->description . ' | ' : '';
            $absence->update([
                'description' => $desc . "Toleransi Telat {$lateMinutes}m (" . $suitableToken->item->item_name . ")",
            ]);

            return [
                'intercepted' => true,
                'message' => "Toleransi Keterlambatan ({$lateMinutes} menit) - Menggunakan " . $suitableToken->item->item_name
            ];
        }

        $message = "Terlambat {$lateMinutes} Menit (Voucher tidak tersedia/cukup)";
        
        // Find previously used token info
        $lastUsed = \App\Models\PointLedger::where('user_id', $user->id)
            ->where('description', 'like', 'Toleransi Keterlambatan%')
            ->latest()
            ->first();

        if ($lastUsed) {
            preg_match('/\((\d+)\s+menit\)/', $lastUsed->description, $match);
            $usedMins = isset($match[1]) ? $match[1] : '?';
            $usedDate = $lastUsed->created_at->timezone('Asia/Jakarta')->format('d M Y');
            $message .= "<br><span class=\"text-[10px] text-gray-500\">*Info: Voucher pernah terpakai tgl {$usedDate} utk menutupi keterlambatan {$usedMins}m.</span>";
        }

        return [
            'intercepted' => false,
            'message' => $message
        ];
    }
}
