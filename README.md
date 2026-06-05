<div align="center">

# 💊 PharmBee
### Sistem Informasi Manajemen Rumah Sakit

> 📚 Tugas Akhir — Mata Kuliah Pemrograman Web Lanjutan

</div>

---

## 📋 Deskripsi Proyek

**PharmBee** adalah aplikasi web manajemen apotek yang dikembangkan untuk mengotomatisasi dan mengintegrasikan alur kerja klinik secara menyeluruh — mulai dari pendaftaran pasien, antrian dokter, pembuatan resep, pengelolaan stok obat, hingga penerbitan invoice pembayaran.

Sistem ini mendukung tiga peran pengguna dengan hak akses yang terpisah, yaitu **Admin**, **Dokter**, dan **Apoteker**, sehingga setiap bagian dapat bekerja secara mandiri namun tetap terintegrasi dalam satu platform.

---

## 👥 Anggota Kelompok

1. GRETHA JOYCE SIBURIAN [251402089]

2. NADHIRA AMEERA SHEREEN NASUTION [251402039]

3. RATU SABRINA TURNIP [251402018]

4. ELYSTA NAOMI NADAPDAP [251402124]

5. SESILIA MARIA GORETTY SIHOMBING [2514020932]

6. EPHIVANI SIMANULLANG [251402086]

7. KEYSAHRANI AMELIA [251402012]

---

## 🎭 Peran dalam Sistem

### 🏥 Admin
- Mendaftarkan pasien baru beserta data diri dan jenis pembayaran
- Memvalidasi data BPJS pasien
- Mengirim pasien ke antrian dokter sesuai poli tujuan
- Mengelola akun dokter
- Membuat dan mengunduh invoice PDF
- Memantau statistik klinik melalui dashboard


### 👨‍⚕️ Dokter
- Melihat antrian pasien sesuai poli
- Mengubah status pemeriksaan pasien
- Membuat resep digital
- Melihat riwayat resep


### 💊 Apoteker
- Mengelola stok obat
- Memproses resep dokter
- Menerima alert stok dan expired obat
- Melihat laporan penggunaan obat

---

## 🖥️ Tampilan Output Sistem

| Halaman | Deskripsi |
|---|---|
| 🔐 Login | Login berdasarkan role pengguna |
| 📊 Dashboard Admin | Statistik pasien, invoice, dan validasi |
| 🩺 Dashboard Dokter | Antrian pasien dan resep terbaru |
| 💊 Dashboard Apoteker | Monitoring stok dan resep |
| 🗂️ Data Pasien | Manajemen data pasien |
| 📝 Resep | Pembuatan resep digital |
| 📦 Stok Obat | Pengelolaan obat |
| 🧾 Invoice | Pembayaran pasien |
| 📈 Laporan | Statistik sistem |

---

## 🚀 Cara Menjalankan

```bash
composer install

npm install

npm run dev

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan serve
```

Akses aplikasi:

```
http://localhost:8000
```

---

## 🙏 Penutup

Demikian dokumentasi proyek **PharmBee** yang dikembangkan sebagai tugas akhir mata kuliah Pemrograman Web Lanjutan.

---

<div align="center">

© 2026 PharmBee — Kelompok 7

</div>