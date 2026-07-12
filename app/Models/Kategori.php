<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;

    protected $table = 'kategori';

    protected $fillable = [
        'kode_ddc',
        'nama_kategori'
    ];

    public function buku()
    {
        return $this->belongsToMany(Buku::class, 'buku_kategori');
    }
}
