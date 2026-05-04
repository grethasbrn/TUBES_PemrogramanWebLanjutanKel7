<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_obat',
        'tipe',
        'no_batch',
        'jumlah',
        'harga',
        'harga_bpjs',
        'tgl_expired',
        'tgl_masuk',
        'supplier',
    ];

    protected $casts = [
        'tgl_expired' => 'date',
        'tgl_masuk'   => 'date',
    ];

    // Format harga ke Rupiah
    public function getHargaBpjsFormattedAttribute(): string
    {
        return $this->harga_bpjs > 0
            ? 'Rp ' . number_format($this->harga_bpjs, 0, ',', '.')
            : 'Gratis';
    }

    // Cek apakah mendekati expired (dalam 90 hari)
    public function getMendekatiExpiredAttribute(): bool
    {
        return $this->tgl_expired->diffInDays(now()) <= 90 && $this->tgl_expired->isFuture();
    }

    // Cek apakah sudah expired
    public function getSudahExpiredAttribute(): bool
    {
        return $this->tgl_expired->isPast();
    }
}