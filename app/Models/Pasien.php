<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $fillable = [
        'no_rm',
        'nama',
        'nik',
        'tgl_lahir',
        'jenis',
        'poli_tujuan',
        'status',
        'validasi'
    ];
}