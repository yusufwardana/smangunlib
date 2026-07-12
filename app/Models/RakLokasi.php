<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RakLokasi extends Model
{
    use SoftDeletes;

    protected $table = 'rak_lokasi';

    protected $fillable = [
        'kode_rak',
        'nama_lokasi',
        'deskripsi'
    ];

    public function buku()
    {
        return $this->hasMany(Buku::class);
    }
}
