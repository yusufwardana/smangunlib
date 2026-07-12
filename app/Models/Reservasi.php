<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservasi extends Model
{
    use SoftDeletes;

    protected $table = 'reservasi';

    protected $fillable = [
        'anggota_id',
        'buku_id',
        'tanggal_reservasi',
        'tanggal_kadaluarsa',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_reservasi' => 'datetime',
            'tanggal_kadaluarsa' => 'datetime',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
