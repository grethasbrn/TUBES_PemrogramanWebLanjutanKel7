<?php

namespace App\Models;

use Carbon\Carbon;
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
        'jenis',
        'no_bpjs',
        'alergi',
    ];

    // Relasi ke resep
    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }

    // Helper: hitung usia dari tgl_lahir
    public function getUsiaAttribute(): int
    {
        return Carbon::parse($this->tgl_lahir)->age;
    }

    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class);
    }
}