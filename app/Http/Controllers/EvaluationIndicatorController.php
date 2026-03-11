<?php

namespace App\Http\Controllers;

use App\Models\EvaluationIndicator;
use App\Models\EvaluationDescription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationIndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $indicators = EvaluationIndicator::with('descriptions')->get();
        return view('boss.evaluation-indicators.index', compact('indicators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('boss.evaluation-indicators.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        EvaluationIndicator::create($validated);

        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Indikator berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EvaluationIndicator $evaluationIndicator): View
    {
        return view('boss.evaluation-indicators.edit', compact('evaluationIndicator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EvaluationIndicator $evaluationIndicator)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $evaluationIndicator->update($validated);

        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Indikator berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EvaluationIndicator $evaluationIndicator)
    {
        $evaluationIndicator->delete();
        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Indikator berhasil dihapus.');
    }

    /**
     * Show the form for creating a new description.
     */
    public function createDescription(EvaluationIndicator $indicator): View
    {
        return view('boss.evaluation-indicators.descriptions.create', compact('indicator'));
    }

    /**
     * Store a new description for an indicator.
     */
    public function storeDescription(Request $request, EvaluationIndicator $indicator)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $indicator->descriptions()->create($validated);

        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Deskripsi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a description.
     */
    public function editDescription(EvaluationDescription $description): View
    {
        return view('boss.evaluation-indicators.descriptions.edit', compact('description'));
    }

    /**
     * Update a description.
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
     * Delete a description.
     */
    public function destroyDescription(EvaluationDescription $description)
    {
        $description->delete();
        return redirect()->route('boss.evaluation-indicators.index')->with('success', 'Deskripsi berhasil dihapus.');
    }
}
