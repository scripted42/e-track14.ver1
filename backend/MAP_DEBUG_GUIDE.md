# 🗺️ MAP DEBUG GUIDE - Peta Belum Muncul

## 🔍 **CARA DEBUGGING:**

### **1. Test Map Standalone**
Buka: `http://127.0.0.1:8000/test-map.html`
- Halaman ini untuk testing Leaflet.js secara standalone
- Jika peta muncul di sini, masalah ada di halaman settings
- Jika tidak muncul, masalah ada di CDN atau browser

### **2. Check Browser Console**
1. Buka halaman settings: `http://127.0.0.1:8000/admin/settings`
2. Tekan F12 untuk buka Developer Tools
3. Klik tab "Console"
4. Cari error atau log messages

### **3. Check Network Tab**
1. Di Developer Tools, klik tab "Network"
2. Refresh halaman
3. Cari request ke:
   - `leaflet.css` - harus status 200
   - `leaflet.js` - harus status 200
   - `tile.openstreetmap.org` - harus status 200

### **4. Manual Testing**
Jalankan di browser console:
```javascript
// Check if Leaflet is loaded
console.log('Leaflet loaded:', typeof L !== 'undefined');

// Check map container
console.log('Map container:', document.getElementById('map'));

// Try to initialize map manually
if (typeof L !== 'undefined') {
    const map = L.map('map', {
        center: [-7.250445, 112.768845],
        zoom: 16
    });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    console.log('Map created:', map);
}
```

---

## 🚨 **KEMUNGKINAN MASALAH:**

### **1. CDN Tidak Bisa Diakses**
- **Gejala:** Console error "Failed to load resource"
- **Solusi:** Cek koneksi internet atau gunakan CDN lain

### **2. JavaScript Error**
- **Gejala:** Console error dengan stack trace
- **Solusi:** Cek error message dan perbaiki code

### **3. CSS Tidak Dimuat**
- **Gejala:** Map container ada tapi tidak terlihat
- **Solusi:** Cek apakah leaflet.css dimuat

### **4. Authentication Issue**
- **Gejala:** Redirect ke login page
- **Solusi:** Login dulu ke aplikasi

### **5. Map Container Hidden**
- **Gejala:** Map dibuat tapi tidak terlihat
- **Solusi:** Cek CSS display property

---

## 🔧 **SOLUSI BERDASARKAN ERROR:**

### **Jika Error "Leaflet not loaded":**
```javascript
// Force reload Leaflet
const script = document.createElement('script');
script.src = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js';
document.head.appendChild(script);
```

### **Jika Error "Map container not found":**
```javascript
// Check if container exists
const container = document.getElementById('map');
if (!container) {
    console.error('Map container not found');
}
```

### **Jika Map Kosong:**
```javascript
// Force map resize
if (map) {
    map.invalidateSize();
}
```

---

## 📋 **CHECKLIST DEBUGGING:**

- [ ] Test map standalone (`/test-map.html`)
- [ ] Check browser console untuk error
- [ ] Check network tab untuk failed requests
- [ ] Check authentication (login dulu)
- [ ] Check map container visibility
- [ ] Try manual map initialization
- [ ] Check CSS loading
- [ ] Check JavaScript loading

---

## 🎯 **NEXT STEPS:**

1. **Buka test page:** `http://127.0.0.1:8000/test-map.html`
2. **Cek apakah peta muncul di test page**
3. **Jika muncul:** Masalah ada di halaman settings
4. **Jika tidak muncul:** Masalah ada di CDN atau browser
5. **Laporkan hasil testing**

**Silakan test dan laporkan hasilnya!**
