<?php

namespace App\Http\Controllers;

use App\Models\EvaluationIndicator;
use App\Models\EvaluationDescription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationIndicatorController extends Controller
{
    /**
     * Menampilkan daftar semua kategori kriteria penilaian (Indikator).
     */
    public function index(): View
    {
        // Mengambil semua indikator beserta butir-butir deskripsinya (Eager Loading)
        $indicators = EvaluationIndicator::with('descriptions')->get();
        return view('boss.evaluation-indicators.index', compact('indicators'));
    }

    /**
     * Menampilkan halaman form untuk menambah kategori kriteria baru.
     */
    public function create(): View
    {
        return view('boss.evaluation-indicators.create');
    }

    /**
     * Menyimpan kategori kriteria (Indikator) baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi: nama indikator wajib diisi dan maksimal 255 karakter
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Membuat data baru di tabel evaluation_indicators
        EvaluationIndicator::create($validated);

        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Indikator berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit untuk kategori kriteria tertentu.
     */
    public function edit(EvaluationIndicator $evaluationIndicator): View
    {
        return view('boss.evaluation-indicators.edit', compact('evaluationIndicator'));
    }

    /**
     * Memperbarui data kategori kriteria di database.
     */
    public function update(Request $request, EvaluationIndicator $evaluationIndicator)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Proses update data ke row yang dipilih
        $evaluationIndicator->update($validated);

        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Indikator berhasil diperbarui.');
    }

    /**
     * Menghapus kategori kriteria dari database.
     */
    public function destroy(EvaluationIndicator $evaluationIndicator)
    {
        // Hapus data (juga otomatis menghapus relasi jika diset cascade di database)
        $evaluationIndicator->delete();
        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Indikator berhasil dihapus.');
    }

    /**
     * Menampilkan form untuk menambah butir pertanyaan (deskripsi) di bawah kategori tertentu.
     */
    public function createDescription(EvaluationIndicator $indicator): View
    {
        return view('boss.evaluation-indicators.descriptions.create', compact('indicator'));
    }

    /**
     * Menyimpan butir pertanyaan baru untuk kategori yang dipilih.
     */
    public function storeDescription(Request $request, EvaluationIndicator $indicator)
    {
        // Validasi input nama butir pertanyaan
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Simpan data deskripsi baru yang otomatis terhubung ke ID induk (Indikator)
        $indicator->descriptions()->create($validated);

        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Deskripsi berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit untuk satu butir pertanyaan spesifik.
     */
    public function editDescription(EvaluationDescription $description): View
    {
        return view('boss.evaluation-indicators.descriptions.edit', compact('description'));
    }

    /**
     * Memperbarui teks butir pertanyaan di database.
     */
    public function updateDescription(Request $request, EvaluationDescription $description)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $description->update($validated);

        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Deskripsi berhasil diperbarui.');
    }

    /**
     * Menghapus butir pertanyaan spesifik.
     */
    public function destroyDescription(EvaluationDescription $description)
    {
        $description->delete();
        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Deskripsi berhasil dihapus.');
    }
}
