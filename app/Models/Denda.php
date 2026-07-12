<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Denda extends Model
{
    use SoftDeletes;

    protected $table = 'denda';

    protected $fillable = [
        'detail_peminjaman_id',
        'anggota_id',
        'jumlah_hari_terlambat',
        'tarif_per_hari',
        'total_denda',
        'status_pembayaran',
        'tanggal_bayar',
        'user_id',
        'alasan_waive'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'datetime',
            'tarif_per_hari' => 'decimal:2',
            'total_denda' => 'decimal:2',
        ];
    }

    public function detailPeminjaman()
    {
        return $this->belongsTo(DetailPeminjaman::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function pustakawan()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
