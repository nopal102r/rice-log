# Panduan Debugging Rice-Log

Dokumen ini menjelaskan cara mencari kesalahan (bug) di aplikasi dan cara mencatatnya agar mudah diperbaiki.

---

## 🛠️ 1. Senjata Utama Debugging (Laravel)

Dalam Laravel, ada beberapa cara "ngintip" isi kodingan pas lagi jalan:

### A. Perintah `dd()` (Die and Dump)
Ini cara paling populer. Kodingan bakal langsung **berhenti** di baris itu dan nampilin isi variabelnya.
*   **Contoh penggunaan:**
    ```php
    public function store(Request $request) {
        $data = $request->all();
        dd($data); // <--- Kodingan berhenti di sini dan munculin isi $data di browser
    }
    ```

### B. Perintah `Log::info()`
Gunakan ini kalau kamu nggak ingin kodingan berhenti, tapi ingin mencatat sesuatu di "buku catatan" sistem.
*   **Cara pakai:** 
    1. Tambahkan `use Illuminate\Support\Facades\Log;` di atas file.
    2. Tulis `Log::info('Data masuk: ', $data);`.
*   **Cek hasilnya di:** `storage/logs/laravel.log`.

---

## 🌐 2. Debugging Tampilan (Frontend/Browser)

Kalau error-nya di tampilan atau JavaScript (seperti grafik radar nggak muncul):

1.  **Klik Kanan** di browser -> **Inspect**.
2.  Buka tab **Console**: Lihat apakah ada tulisan merah (error).
3.  Buka tab **Network**: Lihat apakah ada request yang warnanya merah (Gagal konek ke server/database).

---

## 📝 3. Cara Dokumentasi Perbaikan (Workflow)

Setiap kali nemu bug, biasakan pake format ini biar kamu (atau saya) nggak bingung pas mau benerin.

### Langkah-langkah:
1.  **Identifikasi:** Apa yang error? (Contoh: "Bonus nggak masuk ke database").
2.  **Lacak:** Pakai `dd()` di Controller buat liat datanya sampe mana yang bener.
3.  **Catat:** Tulis di file `DEBUG_LOG.md` (template ada di bawah).

---

## 💡 Tips Bertanya ke AI (Antigravity)
Kalau kamu nemu error dan mau saya yang benerin, cukup:
1.  Copy-paste **Error Message**-nya (tulisan merah di browser).
2.  Kasih tau **file mana** yang kira-kira bermasalah.
3.  Saya bakal bantu analisa dan kasih perbaikannya.
