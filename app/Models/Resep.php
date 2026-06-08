<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $fillable = [
        'pasien_id',
        'kunjungan_id',
        'no_resep',
        'diagnosa',
        'catatan_dokter',
        'tanggal_kontrol',
        'status',
        'alasan_tolak',
        'obat_list',
        'stok_dikurangi',
    ];

    protected $casts = [
        'obat_list'       => 'array',
        'tanggal_kontrol' => 'date',
        'stok_dikurangi' => 'boolean',
    ];

    // Relasi ke pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    // Generate nomor resep otomatis
    public static function generateNoResep()
    {
        return \DB::transaction(function () {
            $year  = date('Y');
            $count = self::whereYear('created_at', $year)->lockForUpdate()->count() + 1;
            return 'RX-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        });
    }
}