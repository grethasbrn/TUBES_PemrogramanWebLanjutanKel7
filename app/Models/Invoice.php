<?php

namespace App\Http\Controllers;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'no_invoice', 'resep_id', 'no_rm', 'nama', 'jenis', 'status',
        'subtotal', 'ppn', 'total_tagihan', 'no_referensi', 'diproses_oleh',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rm', 'no_rm');
    }

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class, 'resep_id');
    }

    public function getPoliAttribute()
    {
        return $this->pasien?->poli_tujuan ?? '-';
    }
}