<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPeminjaman extends Model
{
    use SoftDeletes;

    protected $table = 'detail_peminjaman';

    protected $fillable = [
        'peminjaman_id',
        'eksemplar_id',
        'tanggal_kembali',
        'kondisi_kembali',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kembali' => 'date',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function eksemplar()
    {
        return $this->belongsTo(Eksemplar::class);
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}
