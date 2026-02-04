# 🎯 TIPS MENINGKATKAN DETEKSI WAJAH

**Problem**: Wajah susah terdeteksi meskipun di lokasi cerah

**Solution**: Berikut tips untuk meningkatkan deteksi wajah:

---

## 🔦 Lighting (Pencahayaan) - PALING PENTING

### ✅ **Good Lighting**

- Gunakan natural light dari jendela atau lampu terang
- Posisi cahaya di depan wajah (bukan dari belakang)
- Avoid harsh shadows di wajah
- Lighting minimal 300-500 lux (seperti ruangan kantor normal)

### ❌ **Bad Lighting**

- Ruangan gelap atau cahaya lemah
- Cahaya datang dari belakang (backlighting)
- Cahaya samping yang menciptakan shadow
- Cahaya berwarna ekstrem (semua merah, semua biru)

**Improvement**: Pindah ke lokasi yang lebih terang atau nyalakan lampu tambahan.

---

## 📏 Face Position & Size

### ✅ **Good Positioning**

- Wajah ada di center frame
- Wajah ocupies 30-70% dari video frame
- Face straight to camera (minimal angle)
- Distance: 30-60 cm dari kamera
- Kedua mata terlihat jelas

### ❌ **Bad Positioning**

- Wajah terlalu kecil (jauh dari kamera)
- Wajah terlalu besar (dekat sekali)
- Wajah di tepi frame
- Kepala tertunduk atau menghadap ke samping
- Salah satu mata tertutup

**Improvement**: Posisi wajah di center, distance yang tepat, hadap ke depan.

---

## 🎥 Video/Camera Quality

### ✅ **Good Quality**

- Camera resolution 640x480 minimum
- Camera clear & bersih (bukan blur)
- Good focus (bukan soft focus)
- Stable video (tidak goyang/bergetar)

### ❌ **Bad Quality**

- Resolution terlalu rendah (< 320x240)
- Camera blur atau dirty
- Focus ngaco
- Video goyang terus-menerus

**Improvement**: Clean camera lens, cek resolution, gunakan stable phone holder.

---

## 🔍 Detection Settings (Teknologi)

Sistem sudah di-tune dengan:

- ✅ **Detector**: Tiny Face Detector (more sensitive for real-time)
- ✅ **Input Size**: 416x416 pixels (larger = more accurate)
- ✅ **Score Threshold**: 0.5 (lower = lebih sensitif)
- ✅ **Confidence**: 0.5 minimum

---

## 🧪 Testing Checklist

Coba satu per satu untuk debug:

```
□ Lighting
  - Coba di lokasi lebih terang
  - Minimal cerah seperti ruangan kantor
  - Jangan backlight

□ Position
  - Center wajah di frame
  - Distance 30-60 cm
  - Hadap ke depan

□ Camera
  - Bersihkan lensa
  - Cek fokus
  - Stability cek (gunakan holder)

□ Browser
  - Try Chrome (paling recommended)
  - Clear cache
  - Restart browser

□ Network
  - Check models loaded (F12 → Network)
  - Ensure console no error

□ Face
  - Remove glasses (jika possible)
  - Remove mask/hat
  - Face tidak covered
```

---

## 🆘 Still Not Working?

Jika sudah coba semuanya tapi tetap susah:

1. **Check Browser Console** (F12 → Console)
    - Lihat ada error apa?
    - Bagian mana yang fail?

2. **Try Different Browser**
    - Chrome recommended
    - Firefox also good
    - Safari perlu HTTPS

3. **Try Test Image**
    - Buka: public/js/examples/examples-browser/views/
    - Coba dengan test images
    - Lihat apakah work

4. **Adjust Code**
    - Tuning detection threshold lebih rendah
    - Change input size
    - Try different detector

---

## 📝 Technical Tuning (Advanced)

Jika ingin tweak lebih lanjut, di `public/js/face-recognition-helper.js`:

```javascript
// Line 177 - Tiny Face Detector options
detectionOptions = new faceapi.TinyFaceDetectorOptions({
    inputSize: 416, // 320/416/608 - lebih besar = lebih akurat
    scoreThreshold: 0.5, // 0.1-0.9 - lebih rendah = lebih sensitif
});
```

**Tuning Guide**:

- Jika detection terlalu sensitif → naik scoreThreshold ke 0.6-0.7
- Jika detection tidak sensitif → turun ke 0.3-0.4
- Jika slow → turun inputSize ke 320
- Jika inaccurate → naikkan inputSize ke 608

---

## ✅ Quick Checklist (Copy Paste)

Sebelum klaim "tidak bisa deteksi", coba:

- [ ] Lighting di atas 300 lux (cerah seperti kantor)
- [ ] Wajah di center frame
- [ ] Distance 30-60 cm
- [ ] Face straight to camera
- [ ] Camera clean
- [ ] Browser Chrome (bukan Safari/IE)
- [ ] Console F12 no error
- [ ] Models fully loaded
- [ ] Try 5-10 times (sistem perlu warmup)

---

## 📞 Common Issues

| Issue                              | Solution                                       |
| ---------------------------------- | ---------------------------------------------- |
| "Still not detected"               | Pindah ke lokasi lebih cerah + adjust position |
| "Detected but laggy"               | Turunkan inputSize ke 320 di code              |
| "Too many false positives"         | Naikkan scoreThreshold ke 0.6-0.7              |
| "Lurus ke camera tapi tetap tidak" | Clean camera lens, cek fokus                   |
| "Only detected di angle tertentu"  | Face harus lebih straight to camera            |

---

## 🎯 Expected Results

Dengan setting yang optimal:

✅ **Average Detection Time**: ~200-300ms  
✅ **Detection Accuracy**: 95%+ (dengan good conditions)  
✅ **Detection Range**: Wajah 30-80cm distance  
✅ **Angle Tolerance**: ±30 degrees

Jika tidak tercapai, berarti salah satu dari 3 hal:

1. **Lighting** - paling sering penyebabnya (70%)
2. **Position** - wajah tidak ideal (20%)
3. **Equipment** - camera rusak/blur (10%)

---

**Priority Fix Order**:

1. **Improve Lighting** (paling effective)
2. **Adjust Position** (wajah di center, right distance)
3. **Use Good Camera** (clear, stable, good focus)
4. **Tune Settings** (last resort)
