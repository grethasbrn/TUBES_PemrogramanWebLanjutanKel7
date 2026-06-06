<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $table = 'dokters';

    protected $fillable = [
        'nama',
        'spesialisasi',
        'no_telepon',
        'email',
        'status',
    ];
}