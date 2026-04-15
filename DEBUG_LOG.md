# Catatan Perbaikan Bug (DEBUG_LOG.md)

Gunakan file ini untuk mencatat setiap error yang kamu temukan dan bagaimana cara memperbaikinya.

### 📅 2026-03-12 - Eksperimen Debugging Form Evaluasi
*   **Gejala:** Ingin melihat isi variabel `$validated` sebelum disimpan ke database (Latihan Debugging).
*   **Penyebab:** Perintah `dd($validated)` dipasang di `EvaluationBossController.php`.
*   **Solusi:** Berhasil melihat data form di browser. Perintah `dd()` sudah dihapus kembali agar aplikasi normal.
*   **Status:** ✅ Selesai (Latihan Sukses)

---
*(Copy format di atas untuk bug baru)*
