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
            if ($rule->point_modifier < 0 && $rule->condition_operator === '>') {
                if (!$hasUsedLateToken) {
                    $interceptorResult = $this->interceptor->interceptLateness($absence, $rule);
                    
                    if ($interceptorResult['intercepted']) {
                        $hasUsedLateToken = true;
                        // Record a 0 point transaction to show it was tolerated
                        $this->ledger->recordTransaction(
                            $absence->user,
                            'EARN',
                            0,
                            $interceptorResult['message']
                        );
                        continue; // Skip the penalty
                    } else {
                        // Penalty applied but with descriptive message
                        $this->ledger->recordTransaction(
                            $absence->user,
                            'PENALTY',
                            $rule->point_modifier,
                            $interceptorResult['message']
                        );
                        continue;
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
