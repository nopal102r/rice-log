# Penjelasan Gampang: Sistem Raport Karyawan (Evaluasi)

Biar gampang dipahami, kita bayangkan sistem evaluasi ini seperti **Buku Raport Sekolah**.

---

## 🏫 1. Analogi "Buku Raport" (Database & Model)

Bayangkan struktur datanya seperti ini:

1.  **Indikator (Kategori/Mapel)**: Ini seperti **Mata Pelajaran** (contoh: Matematika, Olahraga).
    *   *File:* [EvaluationIndicator.php](file:///c:/xampp/htdocs/rice-log/rice-log/app/Models/EvaluationIndicator.php)
2.  **Deskripsi (Butir Soal)**: Ini seperti **detail materi** yang dinilai (contoh: Perkalian, Lari 100m). Satu Mapel bisa punya banyak materi.
    *   *File:* [EvaluationDescription.php](file:///c:/xampp/htdocs/rice-log/rice-log/app/Models/EvaluationDescription.php)
3.  **Evaluasi (Buku Raport Fisik)**: Ini adalah **lembar raport** si karyawan untuk bulan tertentu. Di sini juga tempat mencatat pesan dari atasan dan "hadiah" (bonus).
    *   *File:* [Evaluation.php](file:///c:/xampp/htdocs/rice-log/rice-log/app/Models/Evaluation.php)
4.  **Rating (Nilai per Materi)**: Ini adalah **angka (1-5)** yang diberikan guru untuk setiap materi tadi.
    *   *File:* [EvaluationRating.php](file:///c:/xampp/htdocs/rice-log/rice-log/app/Models/EvaluationRating.php)

---

## 💰 2. Fitur Bonus Gaji (Uang Jajan Tambahan)

Bonus ini ibarat uang jajan tambahan kalau raportnya bagus. Logikanya ada di sini:

*   **Tempat Penyimpanan:** Disimpan di tabel `evaluations` kolom `bonus`.
*   **Logika Kodingan (Di [EvaluationBossController.php](file:///c:/xampp/htdocs/rice-log/rice-log/app/Http/Controllers/EvaluationBossController.php)):**
    *   **Line 77:** Komputer ngecek kalau bonus yang diinput harus berupa angka dan nggak boleh minus (`min:0`).
    *   **Line 96:** Saat tombol simpan diklik, sistem bakal ngambil angka bonus dari form. Kalau kosong, otomatis dianggap `0` (`$validated['bonus'] ?? 0`).

---

## 🛠️ 3. Jalur Kerja (Logika Controller)

### 👨‍💼 Tugas Bos (Si Pemberi Nilai)
*   **Ngecek Siapa yang Belum Dinilai:** Di [EvaluationBossController.php](file:///c:/xampp/htdocs/rice-log/rice-log/app/Http/Controllers/EvaluationBossController.php) **Line 16-45**, sistem bakal otomatis nyari absen siapa yang sudah ada raportnya dan siapa yang belum buat bulan ini.
*   **Input Nilai Sekaligus:** Di **Line 84-113**, ada sistem namanya `Transaction`. Ini biar kalau pas simpan nilai internet mati di tengah jalan, data nggak bakal rusak (pilihannya: simpan semua atau batal semua).
*   **Hapus Nilai Lama (Edit):** Di **Line 101**, sebelum nilai baru masuk, sistem bakal hapus dulu nilai yang lama biar nggak numpuk kalau si Bos mau ganti nilai.

### 👩‍🔧 Penglihatan Karyawan (Si Penerima Raport)
*   **Grafik Radar (Radar Chart):** Di [EvaluationEmployeeController.php](file:///c:/xampp/htdocs/rice-log/rice-log/app/Http/Controllers/EvaluationEmployeeController.php) **Line 29-77**, sistem ngitung rata-rata nilai per kategori biar bisa jadi grafik sarang laba-laba yang keren di dashboard karyawan.
*   **Gembok Data:** Di **Line 101**, sistem ngecek: "Eh, ini beneran raport kamu bukan?". Kalau bukan punya dia, sistem bakal ngeluarin error `403` (Dilarang masuk).

---

## 🚀 Ringkasan Alur
1.  **Admin** buat kriteria (Mapel & Materi).
2.  **Bos** isi nilai dan **Bonus** buat karyawan.
3.  **Karyawan** buka dashboard dan bisa lihat grafik performa mereka serta berapa bonus yang didapat bulan itu.
