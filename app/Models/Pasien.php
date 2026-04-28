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
        'jenis',
        'no_bpjs',
        'poli_tujuan',
        'jenis_kunjungan',
        'status',
        'validasi',
        'keluhan',
        'riwayat_penyakit',
    ];
}
