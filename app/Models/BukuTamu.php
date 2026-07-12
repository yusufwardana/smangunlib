<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BukuTamu extends Model
{
    use SoftDeletes;

    protected $table = 'buku_tamu';

    protected $fillable = [
        'nama',
        'tipe_pengunjung',
        'no_identitas',
        'tujuan_kunjungan',
        'tanggal_kunjungan'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kunjungan' => 'datetime',
        ];
    }
}
