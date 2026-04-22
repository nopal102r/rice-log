<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorTicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'category'])
            ->latest()
            ->get();
        
        return view('operator.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'category', 'messages.user']);
        
        // Auto-assign to me if no operator assigned
        if (!$ticket->operator_id) {
            $ticket->update(['operator_id' => Auth::id()]);
        }

        return view('operator.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Capture First Reply Time for SLA
        $data = [];
        if (!$ticket->first_replied_at) {
            $data['first_replied_at'] = now();
            // Also update status to 'in_progress' automatically on first reply
            if ($ticket->status === 'open') {
                $data['status'] = 'in_progress';
            }
        }

        if (!empty($data)) {
            $ticket->update($data);
        }

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Balasan terkirim.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $data = ['status' => $validated['status']];

        // Capture Resolution Time for SLA
        if (in_array($validated['status'], ['resolved', 'closed']) && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }

        $ticket->update($data);

        return back()->with('success', 'Status aduan diperbarui.');
    }

    public function getSuggestions(TicketCategory $category)
    {
        $suggestions = $category->suggestions()->get(['id', 'text']);
        return response()->json($suggestions);
    }
}
