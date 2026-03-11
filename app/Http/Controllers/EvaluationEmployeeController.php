<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationIndicator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EvaluationEmployeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get evaluation history
        $evaluations = Evaluation::where('user_id', $user->id)
            ->with(['ratings.description.indicator'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $latestEvaluation = $evaluations->first();
        $chartData = null;

        if ($latestEvaluation) {
            // Group by indicator name
            $indicatorScores = [];
            
            // Get all indicators to ensure they exist on the chart even if not rated (though they should be)
            $allIndicators = EvaluationIndicator::all()->pluck('name')->toArray();
            foreach ($allIndicators as $name) {
                $indicatorScores[$name] = [
                    'sum' => 0,
                    'count' => 0
                ];
            }

            foreach ($latestEvaluation->ratings as $rating) {
                $indicatorName = $rating->description->indicator->name;
                if (!isset($indicatorScores[$indicatorName])) {
                    $indicatorScores[$indicatorName] = ['sum' => 0, 'count' => 0];
                }
                $indicatorScores[$indicatorName]['sum'] += $rating->rating;
                $indicatorScores[$indicatorName]['count'] += 1;
            }

            $labels = [];
            $scores = [];
            foreach ($indicatorScores as $name => $data) {
                $labels[] = $name;
                $scores[] = $data['count'] > 0 ? round($data['sum'] / $data['count'], 2) : 0;
            }

            $chartData = [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Nilai Performa',
                    'data' => $scores,
                    'fill' => true,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'pointBackgroundColor' => 'rgb(54, 162, 235)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgb(54, 162, 235)'
                ]]
            ];
        }

        // Prepare history data for table
        $history = $evaluations->map(function ($ev) {
            $avg = $ev->ratings->count() > 0 ? round($ev->ratings->avg('rating'), 1) : 0;
            return [
                'month' => Carbon::create(null, $ev->month)->format('F'),
                'year' => $ev->year,
                'average' => $avg,
                'feedback' => $ev->feedback
            ];
        });

        return view('employee.evaluations.index', compact('latestEvaluation', 'chartData', 'history'));
    }
}
