# 🗺️ MAP TROUBLESHOOTING GUIDE

## Masalah: Peta Lokasi Absensi Tidak Muncul (Loading Saja)

### ✅ **Perbaikan yang Telah Diterapkan:**

#### **1. Error Handling yang Lebih Baik**
- Menambahkan pengecekan apakah Leaflet.js sudah dimuat
- Error message yang lebih informatif
- Tombol "Coba Lagi" jika gagal

#### **2. CDN Fallback**
- Menambahkan fallback CDN untuk Leaflet.js
- Integrity check untuk keamanan
- Error handling untuk CDN yang tidak bisa diakses

#### **3. Timing Fix**
- Menambahkan `waitForLeaflet()` function
- Menunggu Leaflet.js dimuat sebelum inisialisasi map
- Timeout yang lebih lama (500ms)

#### **4. Map Resize Fix**
- Menambahkan `map.invalidateSize()` setelah map ditampilkan
- Timeout untuk memastikan map ter-render dengan benar

---

## 🔧 **Cara Mengatasi Masalah:**

### **1. Jika Peta Masih Loading:**
```javascript
// Buka browser console (F12) dan jalankan:
initMap();
```

### **2. Jika Error "Leaflet library not loaded":**
- Refresh halaman
- Cek koneksi internet
- Cek apakah CDN bisa diakses

### **3. Jika Peta Kosong:**
```javascript
// Jalankan di console:
map.invalidateSize();
```

### **4. Manual Fix:**
```javascript
// Jika semua gagal, jalankan ini di console:
document.getElementById('mapLoading').style.display = 'none';
document.getElementById('map').style.display = 'block';
setTimeout(() => {
    if (map) map.invalidateSize();
}, 100);
```

---

## 🚀 **Testing:**

### **1. Buka Halaman Pengaturan Sistem**
- URL: `http://127.0.0.1:8000/admin/settings`

### **2. Cek Console (F12)**
- Tidak ada error JavaScript
- Leaflet.js loaded successfully

### **3. Cek Network Tab**
- Leaflet CSS dan JS loaded (status 200)
- Tile requests successful

### **4. Cek Elements**
- Map container visible (`display: block`)
- Map container has proper dimensions

---

## 📋 **Checklist:**

- [x] Leaflet.js CDN dengan fallback
- [x] Error handling yang proper
- [x] Timing fix untuk loading
- [x] Map resize fix
- [x] Debugging tools
- [x] Manual recovery options

---

## 🎯 **Status:**

**Peta seharusnya sudah bisa dimuat dengan benar setelah perbaikan ini.**

Jika masih ada masalah, cek:
1. Koneksi internet
2. Browser console untuk error
3. Network tab untuk failed requests
4. Coba refresh halaman
