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
        // Mengambil data user yang sedang login
        $user = auth()->user();
        
        // Mengambil riwayat evaluasi milik karyawan tersebut
        // Menggunakan with(...) untuk mengambil data rating, deskripsi, dan indikator sekaligus (Eager Loading)
        $evaluations = Evaluation::where('user_id', $user->id)
            ->with(['ratings.description.indicator'])
            ->orderBy('year', 'desc') // Urutkan dari tahun terbaru
            ->orderBy('month', 'desc') // Urutkan dari bulan terbaru
            ->get();

        $latestEvaluation = $evaluations->first(); // Ambil penilaian paling akhir/terbaru
        $chartData = null;

        // Jika ada data penilaian, kita siapkan data untuk Grafik Radar (Spider Chart)
        if ($latestEvaluation) {
            $indicatorScores = [];
            
            // Ambil semua nama indikator yang ada di sistem
            $allIndicators = EvaluationIndicator::all()->pluck('name')->toArray();
            foreach ($allIndicators as $name) {
                // Inisialisasi skor awal: 0 sum dan 0 count
                $indicatorScores[$name] = [
                    'sum' => 0,
                    'count' => 0
                ];
            }

            // Looping setiap rating pada penilaian terbaru untuk menghitung rata-rata per kategori
            foreach ($latestEvaluation->ratings as $rating) {
                $indicatorName = $rating->description->indicator->name;
                if (!isset($indicatorScores[$indicatorName])) {
                    $indicatorScores[$indicatorName] = ['sum' => 0, 'count' => 0];
                }
                // Menjumlahkan nilai rating dan menghitung berapa banyak butir yang dinilai
                $indicatorScores[$indicatorName]['sum'] += $rating->rating;
                $indicatorScores[$indicatorName]['count'] += 1;
            }

            $labels = [];
            $scores = [];
            // Menghitung nilai akhir (rata-rata) per kategori untuk ditampilkan di grafik
            foreach ($indicatorScores as $name => $data) {
                $labels[] = $name;
                // Rumus: Total Nilai / Jumlah Butir (dibulatkan 2 angka di belakang koma)
                $scores[] = $data['count'] > 0 ? round($data['sum'] / $data['count'], 2) : 0;
            }

            // Format data sesuai kebutuhan library Chart.js
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

        // Menyiapkan data riwayat untuk tabel di halaman index
        $history = $evaluations->map(function ($ev) {
            // Hitung rata-rata nilai keseluruhan raport
            $avg = $ev->ratings->count() > 0 ? round($ev->ratings->avg('rating'), 1) : 0;
            return [
                'id' => $ev->id,
                'month' => Carbon::create(null, $ev->month)->format('F'), // Ubah angka bulan jadi nama bulan
                'year' => $ev->year,
                'average' => $avg,
                'feedback' => $ev->feedback,
                'bonus' => $ev->bonus,
            ];
        });

        // Tampilkan halaman daftar evaluasi karyawan
        return view('employee.evaluations.index', compact('latestEvaluation', 'chartData', 'history'));
    }

    public function show(Evaluation $evaluation)
    {
        // Fitur Keamanan: Pastikan karyawan cuma bisa lihat raport miliknya sendiri
        // Jika mencoba melihat ID raport orang lain via URL, sistem akan memblokir (403)
        if ($evaluation->user_id !== auth()->id()) {
            abort(403);
        }

        // Mengambil data detail rating beserta keterangannya
        $evaluation->load(['ratings.description.indicator']);

        // Mengelompokkan butir-butir nilai berdasarkan kategori Indikator
        // Biar di tampilan web bisa rapi per-seksi (Misal: Bagian Kedisiplinan isinya apa saja)
        $groupedRatings = $evaluation->ratings->groupBy(function($item) {
            return $item->description->indicator->name;
        });

        // Tampilkan halaman detail rincian nilai
        return view('employee.evaluations.show', compact('evaluation', 'groupedRatings'));
    }
}
