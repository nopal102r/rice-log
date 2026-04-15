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
        // Mendapatkan data waktu sekarang (bulan dan tahun) menggunakan Carbon
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        // Mengambil semua user dengan role 'karyawan' yang statusnya masih aktif
        $employees = User::where('role', 'karyawan')->where('status', 'active')->get();
        $totalEmployees = $employees->count();

        // Mencari data evaluasi yang sudah dibuat untuk bulan dan tahun ini
        // keyBy('user_id') digunakan agar kita bisa ngecek data berdasarkan ID user dengan mudah
        $evaluations = Evaluation::where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('user_id');

        // Menghitung jumlah karyawan yang sudah dinilai dan persentase progresnya
        $ratedCount = $evaluations->count();
        $progress = $totalEmployees > 0 ? ($ratedCount / $totalEmployees) * 100 : 0;

        // Membuat daftar karyawan dan status apakah mereka sudah dinilai atau belum
        $employeeList = $employees->map(function ($employee) use ($evaluations) {
            return [
                'user' => $employee,
                'is_rated' => $evaluations->has($employee->id), // Cek apakah ID karyawan ada di data evaluasi
            ];
        });

        // Mengirim data ke view dashboard evaluasi milik Boss
        return view('boss.evaluations.index', compact('employeeList', 'ratedCount', 'totalEmployees', 'progress', 'month', 'year'));
    }

    public function create(User $user)
    {
        // Mendapatkan bulan dan tahun berjalan
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        // Mengambil semua kategori indikator beserta butir deskripsinya (Eager Loading)
        $indicators = EvaluationIndicator::with('descriptions')->get();
        
        // Mencari apakah sudah ada penilaian untuk karyawan ini di bulan/tahun yang sama
        $existingEvaluation = Evaluation::where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year)
            ->with('ratings')
            ->first();

        // Menampilkan form penilaian
        return view('boss.evaluations.form', compact('user', 'indicators', 'existingEvaluation', 'month', 'year'));
    }

    public function store(Request $request)
    {
        // Validasi input dari form: pastikan semua data sesuai aturan (misal: rating harus 1-5)
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'feedback' => 'nullable|string',
            'bonus' => 'nullable|numeric|min:0', // Bonus harus angka dan minimal 0
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:5',
            'next' => 'nullable|boolean',
        ]);

        // Mulai transaksi database: memastikan data tersimpan semua atau tidak sama sekali (mencegah error parsial)
        \DB::beginTransaction();
        try {
            // Update data jika sudah ada, atau buat baru jika belum ada (updateOrCreate)
            $evaluation = Evaluation::updateOrCreate(
                [
                    'user_id' => $validated['user_id'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                ],
                [
                    'boss_id' => auth()->id(), // Mencatat siapa atasan yang memberi poin
                    'feedback' => $validated['feedback'],
                    'bonus' => $validated['bonus'] ?? 0, // Jika bonus kosong, set jadi 0
                ]
            );

            // Hapus rating lama sebelum memasukkan rating baru (biar data nggak numpuk/double saat edit)
            $evaluation->ratings()->delete();

            // Looping untuk menyimpan setiap nilai per butir indikator
            foreach ($validated['ratings'] as $descId => $rating) {
                EvaluationRating::create([
                    'evaluation_id' => $evaluation->id,
                    'evaluation_description_id' => $descId,
                    'rating' => $rating,
                ]);
            }

            // Jika semua proses di atas berhasil, terapkan perubahan ke database
            \DB::commit();

            // Jika user mencentang opsi "Lanjut ke karyawan berikutnya"
            if ($request->has('next') && $request->next) {
                // Cari karyawan lain yang aktif dan belum dinilai untuk bulan ini
                $nextEmployee = User::where('role', 'karyawan')
                    ->where('status', 'active')
                    ->whereDoesntHave('evaluations', function ($query) use ($validated) {
                        $query->where('month', $validated['month'])->where('year', $validated['year']);
                    })
                    ->first();

                // Jika ketemu, langsung arahkan ke form penilaian karyawan tersebut
                if ($nextEmployee) {
                    return redirect()->route('boss.evaluations.create', $nextEmployee->id)
                        ->with('success', 'Penilaian berhasil disimpan. Silahkan lanjut ke karyawan berikutnya.');
                }
            }

            // Jika tidak ada opsi lanjut, kembali ke halaman utama evaluasi
            return redirect()->route('boss.evaluations.index')->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan yang sempat masuk ke database (Rollback)
            \DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
