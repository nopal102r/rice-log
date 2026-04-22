<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->tickets()->with('category')->latest()->get();
        return view('employee.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = TicketCategory::all();
        return view('employee.tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = Auth::user()->tickets()->create($validated);

        // Initial message (the description itself is the first content)
        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['description'],
        ]);

        return redirect()->route('employee.tickets.show', $ticket)->with('success', 'Aduan berhasil dibuat.');
    }

    public function checkDuplicate(Request $request)
    {
        $search = $request->input('subject') . ' ' . $request->input('description');
        
        if (strlen(trim($search)) < 5) return response()->json([]);

        // Perform Full-Text Search
        $tickets = Ticket::whereRaw("MATCH(subject, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$search])
            ->where('status', '!=', 'closed')
            ->limit(3)
            ->get(['id', 'subject', 'status']);

        return response()->json($tickets);
    }

    public function show(Ticket $ticket)
    {
        // Allowed for all authenticated users to support viewing duplicate tickets
        $ticket->load(['category', 'messages.user', 'operator']);
        return view('employee.tickets.show', compact('ticket'));
    }

    public function sendMessage(Request $request, Ticket $ticket)
    {
        $this->authorizeOwner($ticket);

        if ($ticket->status === 'closed') {
            return back()->with('error', 'Tiket sudah ditutup.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }

    public function rate(Request $request, Ticket $ticket)
    {
        $this->authorizeOwner($ticket);

        if ($ticket->status !== 'closed' && $ticket->status !== 'resolved') {
            return back()->with('error', 'Hanya bisa memberikan rating pada tiket yang sudah selesai.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $ticket->update(['rating' => $validated['rating']]);

        return back()->with('success', 'Terima kasih atas penilaian Anda!');
    }

    private function authorizeOwner(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Hanya pemilik aduan yang dapat melakukan aksi ini.');
        }
    }
}
