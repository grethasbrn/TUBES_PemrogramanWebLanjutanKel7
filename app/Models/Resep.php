<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $fillable = [
        'pasien_id',
        'no_resep',
        'diagnosa',
        'catatan_dokter',
        'tanggal_kontrol',
        'status',
        'obat_list',
    ];

    protected $casts = [
        'obat_list' => 'array',  // otomatis encode/decode JSON
    ];

    // Relasi ke pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    // Generate nomor resep otomatis
    public static function generateNoResep()
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'RX-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}