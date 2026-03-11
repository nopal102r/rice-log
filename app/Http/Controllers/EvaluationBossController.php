<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluation;
use App\Models\EvaluationIndicator;
use App\Models\EvaluationRating;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EvaluationBossController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $employees = User::where('role', 'karyawan')->where('status', 'active')->get();
        $totalEmployees = $employees->count();

        $evaluations = Evaluation::where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('user_id');

        $ratedCount = $evaluations->count();
        $progress = $totalEmployees > 0 ? ($ratedCount / $totalEmployees) * 100 : 0;

        $employeeList = $employees->map(function ($employee) use ($evaluations) {
            return [
                'user' => $employee,
                'is_rated' => $evaluations->has($employee->id),
            ];
        });

        return view('boss.evaluations.index', compact('employeeList', 'ratedCount', 'totalEmployees', 'progress', 'month', 'year'));
    }

    public function create(User $user)
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $indicators = EvaluationIndicator::with('descriptions')->get();
        
        // Find existing evaluation if any
        $existingEvaluation = Evaluation::where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year)
            ->with('ratings')
            ->first();

        return view('boss.evaluations.form', compact('user', 'indicators', 'existingEvaluation', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'feedback' => 'nullable|string',
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:5',
            'next' => 'nullable|boolean',
        ]);

        \DB::beginTransaction();
        try {
            $evaluation = Evaluation::updateOrCreate(
                [
                    'user_id' => $validated['user_id'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                ],
                [
                    'boss_id' => auth()->id(),
                    'feedback' => $validated['feedback'],
                ]
            );

            // Clear old ratings if updating
            $evaluation->ratings()->delete();

            foreach ($validated['ratings'] as $descId => $rating) {
                EvaluationRating::create([
                    'evaluation_id' => $evaluation->id,
                    'evaluation_description_id' => $descId,
                    'rating' => $rating,
                ]);
            }

            \DB::commit();

            if ($request->has('next') && $request->next) {
                // Find next unrated employee
                $nextEmployee = User::where('role', 'karyawan')
                    ->where('status', 'active')
                    ->whereDoesntHave('evaluations', function ($query) use ($validated) {
                        $query->where('month', $validated['month'])->where('year', $validated['year']);
                    })
                    ->first();

                if ($nextEmployee) {
                    return redirect()->route('boss.evaluations.create', $nextEmployee->id)
                        ->with('success', 'Penilaian berhasil disimpan. Silahkan lanjut ke karyawan berikutnya.');
                }
            }

            return redirect()->route('boss.evaluations.index')->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
