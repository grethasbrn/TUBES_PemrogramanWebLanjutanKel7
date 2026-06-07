<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kunjungan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasien_id',
        'dokter_id',
        'no_kunjungan',
        'poli_tujuan',
        'jenis_kunjungan',
        'keluhan',
        'riwayat_penyakit',
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah',
        'status',
        'validasi',
        'status_kirim',
    ];

    // ── Relasi ────────────────────────────────────────────
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // ── Generate no_kunjungan otomatis ────────────────────
    public static function generateNoKunjungan(): string
    {
        return \DB::transaction(function () {
            $today = date('dmy');
            $count = self::whereDate('created_at', today())
                ->lockForUpdate()
                ->count();
            return 'KNJ-' . $today . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        });
    }
}