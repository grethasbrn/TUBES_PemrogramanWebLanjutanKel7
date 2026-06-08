<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $fillable = [
        'pasien_id',
        'kunjungan_id',   // ← pastikan ini ada
        'no_resep',
        'diagnosa',
        'catatan_dokter',
        'tanggal_kontrol',
        'status',
        'alasan_tolak',
        'obat_list',
    ];

    protected $casts = [
        'obat_list'       => 'array',
        'tanggal_kontrol' => 'date',
    ];

    // Relasi ke pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    // ✅ FIX: Resep belongsTo Kunjungan (bukan hasMany)
    // Bug lama: public function kunjungans() { return $this->hasMany(Kunjungan::class); }
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    // Generate nomor resep otomatis
    public static function generateNoResep()
    {
        $year  = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'RX-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}