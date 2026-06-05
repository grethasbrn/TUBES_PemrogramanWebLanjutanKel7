<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_rm',
        'nama',
        'nik',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'pekerjaan',
        'jenis',           // BPJS atau Mandiri
        'no_bpjs',
        'poli_tujuan',
        'jenis_kunjungan',
        'status',
        'validasi',
        'status_kirim', 
        'dokter', 
        'keluhan',
        'riwayat_penyakit',
        // Kolom medis baru untuk dokter
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah',
        'alergi',
    ];

    // Relasi: satu pasien bisa punya banyak resep
    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }

    // Helper: hitung usia dari tgl_lahir
    public function getUsiaAttribute()
    {
        return \Carbon\Carbon::parse($this->tgl_lahir)->age;
    }
}