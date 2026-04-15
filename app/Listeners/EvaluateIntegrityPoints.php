<?php

namespace App\Listeners;

use App\Events\AbsenceSaved;
use App\Services\PointLedgerService;
use App\Services\RuleEngineService;
use App\Services\TokenInterceptorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EvaluateIntegrityPoints
{
    protected RuleEngineService $ruleEngine;
    protected TokenInterceptorService $interceptor;
    protected PointLedgerService $ledger;

    /**
     * Create the event listener.
     */
    public function __construct(RuleEngineService $ruleEngine, TokenInterceptorService $interceptor, PointLedgerService $ledger)
    {
        $this->ruleEngine = $ruleEngine;
        $this->interceptor = $interceptor;
        $this->ledger = $ledger;
    }

    /**
     * Handle the event.
     */
    public function handle(AbsenceSaved $event): void
    {
        $absence = $event->absence;
        
        $matchedRules = $this->ruleEngine->getMatchedRules($absence);
        $hasUsedLateToken = false;

        foreach ($matchedRules as $rule) {
            if ($rule->point_modifier < 0) {
                if (!$hasUsedLateToken) {
                    if ($this->interceptor->interceptLateness($absence)) {
                        $hasUsedLateToken = true;
                        continue; // Skip applying this penalty
                    }
                }
            }
            
            $type = $rule->point_modifier >= 0 ? 'EARN' : 'PENALTY';
            $this->ledger->recordTransaction(
                $absence->user,
                $type,
                $rule->point_modifier,
                "Automated Rule: {$rule->rule_name}"
            );
        }
    }
}
