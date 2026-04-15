# Panduan Testing: Quality Control (QC) Aplikasi

Kalau **Debugging** itu seperti mencari penyakit saat orang sudah sakit, **Testing** itu seperti **Check-up rutin**/QC biar kita yakin aplikasi nggak rusak pas ada perubahan.

---

## 🏎️ 1. Analogi Gampang
Bayangkan kita sedang membuat mobil:
*   **Unit Testing**: Kita ngetes bautnya kuat nggak? Bannya bocor nggak? (Tes bagian terkecil kodingan).
*   **Feature Testing**: Kita test drive mobilnya. Bisa jalan nggak? Remnya pakem nggak? (Tes satu alur utuh, misal: Alur Simpan Nilai).

---

## 🛠️ 2. Cara Kerja Testing di Laravel
Kita menggunakan alat bernama **Pest** atau **PHPUnit**. Kodingan tes kita ditaruh di folder `tests/`.

### Alur Proses:
1.  **Buat File Tes**: Kita tulis apa yang mau kita tes (Harapannya apa, hasilnya apa).
2.  **Jalankan Perintah**: Kita panggil "satpam" (Robot Checker) buat ngetes otomatis.
3.  **Lihat Hasil**: 
    *   🟢 **PASS (Hijau)**: Aman, kodingan kamu sehat.
    *   🔴 **FAIL (Merah)**: Ada yang rusak, harus segera dibenerin.

---

## 🚀 3. Contoh Cara Ngetes Evaluasi
Misal kita mau ngetes: *"Apakah Boss bisa buka halaman input nilai?"*

1.  Robot akan berpura-pura jadi Boss.
2.  Robot akan mencoba buka link `/boss/evaluations`.
3.  Kalau halamannya muncul (Status 200), berarti **Lolos QC**.

---

## 💻 4. Perintah Menjalankan Tes
Kamu bisa jalankan perintah ini di terminal:
```bash
php artisan test
```
Atau kalau mau tes file tertentu saja:
```bash
php artisan test tests/Feature/EvaluationTest.php
```

---

## ✅ 5. Kenapa Ini Penting?
Biar kamu nggak perlu capek-capek klik sana-sini secara manual setiap kali habis ganti kodingan. Cukup jalankan satu perintah, robot bakal ngetes semua fitur kamu dalam hitungan detik!
