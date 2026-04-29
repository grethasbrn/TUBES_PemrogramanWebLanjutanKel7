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
        'tgl_expired',
        'tgl_masuk',
        'supplier',
    ];

    protected $casts = [
        'tgl_expired' => 'date',
        'tgl_masuk'   => 'date',
    ];

    // Format harga ke Rupiah
    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
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