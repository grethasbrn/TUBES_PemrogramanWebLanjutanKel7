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

4. ELYSTA NAOMI NADAPDAP 251402124 

5. SESILIA MARIA GORETTY SIHOMBING [2514020932] 

6. EPHIVANI SIMANULLANG [251402086] 

7. KEYSAHRANI AMELIA [251402012]

---

## 🎭 Peran dalam Sistem

<table>
<tr>
<td width="33%" valign="top">

### 🏥 Admin
- Mendaftarkan pasien baru beserta data diri dan jenis pembayaran
- Memvalidasi data BPJS pasien
- Mengirim pasien ke antrian dokter sesuai poli tujuan
- Mengelola akun dokter (tambah, ubah, hapus)
- Membuat dan mengunduh invoice (PDF)
- Memantau statistik klinik melalui dashboard

</td>
<td width="33%" valign="top">

### 👨‍⚕️ Dokter
- Melihat antrian pasien sesuai poli yang ditangani
- Memperbarui status pasien (Menunggu → Diperiksa → Selesai)
- Membuat resep digital beserta diagnosa dan daftar obat
- Memantau riwayat resep yang telah dibuat

</td>
<td width="33%" valign="top">

### 💊 Apoteker
- Mengelola stok obat per batch (harga, jumlah, expired, supplier)
- Memproses resep yang masuk dari dokter
- Menerima notifikasi stok menipis atau obat hampir expired
- Melihat laporan penjualan dan pemakaian obat

</td>
</tr>
</table>

---

## 🖥️ Tampilan Output Sistem

| Halaman | Deskripsi |
|---------|-----------|
| 🔐 **Login** | Halaman autentikasi. Sistem mengarahkan pengguna ke dashboard sesuai perannya secara otomatis. |
| 📊 **Dashboard Admin** | Statistik klinik secara real-time: pasien hari ini, antrian aktif, total invoice, dan status validasi BPJS. |
| 🩺 **Dashboard Dokter** | Antrian pasien difilter otomatis berdasarkan poli dokter yang login, beserta 5 resep terbaru. |
| 💉 **Dashboard Apoteker** | Ringkasan stok obat, resep masuk yang belum diproses, dan notifikasi stok/expired. |
| 🗂️ **Manajemen Pasien** | Tabel data seluruh pasien dengan fitur validasi BPJS, pengiriman ke dokter, edit, dan hapus. |
| 🔢 **Antrian** | Daftar pasien yang sudah divalidasi dan siap dikirim ke dokter. |
| 📝 **Resep** | Form resep digital: pilih pasien, isi diagnosa, tambahkan obat dari stok yang tersedia. |
| 📦 **Stok / Batch** | Inventaris obat lengkap dengan harga, kuantitas, kategori, dan tanggal expired. |
| 🚨 **Alerts** | Notifikasi otomatis untuk obat yang stoknya menipis atau sudah/hampir expired. |
| 🧾 **Invoice** | Daftar tagihan pasien dengan fitur buat invoice baru dan unduh PDF. |
| 📈 **Laporan** | Grafik dan statistik penggunaan obat serta pendapatan klinik per periode. |

---

## 🚀 Cara Menjalankan

```bash
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

> Akses aplikasi di **http://localhost:8000**

---

## 🙏 Penutup

Demikian dokumentasi proyek **PharmBee** yang kami kembangkan sebagai tugas akhir mata kuliah Pemrograman Web. Kami menyadari sistem ini masih memiliki ruang untuk dikembangkan lebih lanjut, dan kami terbuka terhadap kritik serta saran yang membangun.

---

<div align="center">

*© 2026 PharmBee — Kelompok 7*

</div>
