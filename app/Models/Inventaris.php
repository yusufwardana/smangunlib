<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaris extends Model
{
    use SoftDeletes;

    protected $table = 'inventaris';

    protected $fillable = [
        'nomor_inventaris',
        'nama_barang',
        'kategori_barang',
        'jumlah',
        'kondisi',
        'tahun_pengadaan',
        'sumber_dana',
        'keterangan'
    ];
}
