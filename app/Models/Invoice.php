<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'kode_invoice',
        'pasien_id',
        'tanggal',
        'status',
        'bayar',
        'total'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }
}