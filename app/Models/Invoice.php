<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'created_at',
        'updated_at',
        'diproses_oleh'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function resep(): BelongsTo 
    {
        return $this->belongsTo(Resep::class, 'resep_id');
    }

    public function getPpnAttribute(): float
    {
        return $this->jenis === 'Mandiri' ? round($this->subtotal * 0.11) : 0;
    }

    public function getTotalTagihanFinalAttribute(): float
    {
        return $this->subtotal + $this->getPpnAttribute();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
