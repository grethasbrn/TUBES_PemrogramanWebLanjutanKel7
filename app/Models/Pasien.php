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
    ];

    // Relasi ke kunjungan
    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class);
    }

    // Helper: hitung usia dari tgl_lahir
    public function getUsiaAttribute()
    {
        return \Carbon\Carbon::parse($this->tgl_lahir)->age;
    }
}