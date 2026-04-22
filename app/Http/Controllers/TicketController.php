<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @class TicketController
 * @brief Controller untuk mengelola siklus hidup tiket/aduan dari sisi Pelapor (Karyawan).
 */
class TicketController extends Controller
{
    /**
     * @brief Menampilkan daftar semua aduan milik pengguna yang sedang login.
     * @return \Illuminate\View\View Halaman indeks tiket pelapor.
     */
    public function index()
    {
        $tickets = Auth::user()->tickets()->with('category')->latest()->get();
        return view('employee.tickets.index', compact('tickets'));
    }

    /**
     * @brief Menampilkan form untuk membuat aduan baru.
     * @return \Illuminate\View\View Halaman form buat tiket.
     */
    public function create()
    {
        $categories = TicketCategory::all();
        return view('employee.tickets.create', compact('categories'));
    }

    /**
     * @brief Menyimpan aduan baru ke database dan membuat pesan awal.
     * @param Request $request Data input meliputi category_id, subject, dan description.
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman detail tiket yang baru dibuat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Hard Block Duplicate Check (Exact or Phrase Match)
        $subject = $validated['subject'];
        $hasDuplicate = Ticket::where('status', '!=', 'closed')
            ->where(function($q) use ($subject) {
                // Exact string match (very reliable for exact duplicates)
                $q->where('subject', 'LIKE', $subject)
                // Or phrase match using the existing (subject, description) index
                  ->orWhereRaw("MATCH(subject, description) AGAINST(? IN BOOLEAN MODE)", ['"' . $subject . '"']);
            })
            ->exists();

        if ($hasDuplicate) {
            return back()->withInput()->withErrors([
                'subject' => 'Aduan serupa ditemukan! Mohon gunakan aduan yang sudah ada untuk menghindari duplikasi data.'
            ]);
        }

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
        $subject = trim($request->input('subject', ''));
        
        // Reduced threshold to 3 characters for better detection of short titles
        if (strlen($subject) < 3) return response()->json([]);

        $description = trim($request->input('description', ''));
        
        $tickets = Ticket::where('status', '!=', 'closed')
            ->where(function($q) use ($subject, $description) {
                // Priority 1: Exact/Partial Subject Match (Standard SQL)
                $q->where('subject', 'LIKE', '%' . $subject . '%')
                // Priority 2: Match whole subject phrase (Using existing FTS index)
                  ->orWhereRaw("MATCH(subject, description) AGAINST(? IN BOOLEAN MODE)", ['"' . $subject . '"']);
                
                // Priority 3: Match descriptive keywords if long enough
                if (strlen($subject . $description) > 10) {
                    $q->orWhereRaw("MATCH(subject, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$subject . ' ' . $description]);
                }
            })
            ->limit(3)
            ->get(['id', 'subject', 'status']);

        return response()->json($tickets);
    }

    /**
     * @brief Menampilkan detail aduan beserta riwayat percakapan.
     * @param Ticket $ticket Objek tiket yang dipilih.
     * @return \Illuminate\View\View Halaman detail tiket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['category', 'messages.user', 'operator']);
        return view('employee.tickets.show', compact('ticket'));
    }

    /**
     * @brief Mengirimkan pesan balasan (komunikasi dua arah) dalam sebuah tiket.
     * @param Request $request Data input berupa isi pesan.
     * @param Ticket $ticket Tiket yang dituju.
     * @return \Illuminate\Http\RedirectResponse Kembali ke halaman tiket dengan status sukses/gagal.
     */
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

    /**
     * @brief Memberikan rating kepuasan pelapor setelah tiket diselesaikan.
     * @param Request $request Data input berupa angka rating (1-5).
     * @param Ticket $ticket Tiket yang telah selesai.
     * @return \Illuminate\Http\RedirectResponse Kembali ke halaman tiket.
     */
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

    /**
     * @brief Validasi internal untuk memastikan hanya pemilik tiket yang bisa berinteraksi.
     * @param Ticket $ticket Objek tiket yang dicek.
     * @return void Menghentikan eksekusi dengan abort(403) jika bukan pemilik.
     */
    private function authorizeOwner(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Hanya pemilik aduan yang dapat melakukan aksi ini.');
        }
    }
}