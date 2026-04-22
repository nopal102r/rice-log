<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketAnalyticsController extends Controller
{
    public function index()
    {
        // 1. Avg Response Time per Operator
        $operatorPerformance = User::whereIn('role', ['operator', 'bos'])
            ->withCount('handledTickets')
            ->get()
            ->map(function ($operator) {
                $tickets = $operator->handledTickets()->whereNotNull('first_replied_at')->get();
                $avgResponse = $tickets->avg(fn($t) => $t->getResponseTimeInMinutes());
                $avgResolution = $tickets->whereNotNull('resolved_at')->avg(fn($t) => $t->getResolutionTimeInMinutes());
                $avgRating = $tickets->whereNotNull('rating')->avg('rating');

                return [
                    'name' => $operator->name,
                    'count' => $operator->handled_tickets_count,
                    'avg_response' => round($avgResponse ?? 0, 1),
                    'avg_resolution' => round($avgResolution ?? 0, 1),
                    'avg_rating' => round($avgRating ?? 0, 1),
                ];
            });

        // 2. CSAT (Customer Satisfaction Score)
        $csat = Ticket::whereNotNull('rating')->avg('rating') ?: 0;

        // 3. Problem Trends (Category distribution)
        $trends = DB::table('tickets')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->select('ticket_categories.name', DB::raw('count(*) as count'))
            ->groupBy('ticket_categories.name')
            ->orderByDesc('count')
            ->get();

        // 4. Ticket Status Summary
        $statusSummary = Ticket::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('boss.tickets.analytics', compact('operatorPerformance', 'csat', 'trends', 'statusSummary'));
    }
}
