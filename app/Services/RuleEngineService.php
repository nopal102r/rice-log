<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\PointRule;
use Carbon\Carbon;

class RuleEngineService
{
    /**
     * Get rules that match the absence conditions.
     */
    public function getMatchedRules(Absence $absence): array
    {
        if ($absence->type !== 'masuk') return [];
        // Skip rules if attendance is rejected
        if ($absence->status_approval === 'rejected') return [];
        // Only evaluate if it's considered an attendance or manual approval that resulted in hadir/sakit/izin/alpa
        if (!in_array($absence->status, ['hadir', 'sakit', 'izin', 'alpa'])) return [];

        $user = $absence->user;
        $rules = PointRule::all();
        $timeStr = $absence->created_at->format('H:i');
        
        $matched = [];

        foreach ($rules as $rule) {
            // Skip if role specified and does not match (assuming boss/employee string compare)
            // Wait, our User isBoss() and isEmployee() logic: Boss = 'bos', Employee = 'karyawan'
            if ($rule->target_role && $user->role !== $rule->target_role) continue;
            
            if ($this->evaluateCondition($absence, $rule->condition_operator, $rule->condition_value)) {
                $matched[] = $rule;
            }
        }

        return $matched;
    }

    private function evaluateCondition(Absence $absence, string $operator, string $value): bool
    {
        if ($operator === 'STATUS_EQUALS') {
            return strtolower($absence->status) === strtolower($value);
        }

        $timeStr = $absence->created_at->timezone('Asia/Jakarta')->format('H:i');
        $checkTime = Carbon::createFromFormat('H:i', $timeStr);
        
        try {
            if ($operator === '<') {
                $ruleTime = Carbon::createFromFormat('H:i', $value);
                return $checkTime->lessThan($ruleTime);
            } elseif ($operator === '>') {
                $ruleTime = Carbon::createFromFormat('H:i', $value);
                return $checkTime->greaterThan($ruleTime);
            } elseif ($operator === 'BETWEEN') {
                $parts = explode(',', $value);
                if (count($parts) == 2) {
                    $start = Carbon::createFromFormat('H:i', trim($parts[0]));
                    $end = Carbon::createFromFormat('H:i', trim($parts[1]));
                    return $checkTime->between($start, $end);
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
