<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'no_invoice',
        'resep_id',
        'no_rm',
        'nama',
        'jenis',
        'status',
        'subtotal',
        'total_tagihan',
        'no_referensi',
        'diproses_oleh',
    ];

    /**
     * Relasi ke Pasien via no_rm
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rm', 'no_rm');
    }

    /**
     * Accessor: ambil poli dari pasien
     */
    public function getPoliAttribute()
    {
        return $this->pasien?->poli_tujuan ?? '-';
    }
}
