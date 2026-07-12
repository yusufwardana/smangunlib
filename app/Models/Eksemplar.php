<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eksemplar extends Model
{
    use SoftDeletes;

    protected $table = 'eksemplar';

    protected $fillable = [
        'buku_id',
        'nomor_barcode',
        'tanggal_pengadaan',
        'asal_pengadaan',
        'harga',
        'kondisi',
        'status_sirkulasi'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengadaan' => 'date',
            'harga' => 'decimal:2',
        ];
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
