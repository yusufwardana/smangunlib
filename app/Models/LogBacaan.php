<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogBacaan extends Model
{
    use SoftDeletes;

    protected $table = 'log_bacaan';

    protected $fillable = [
        'peserta_literasi_id',
        'buku_id',
        'judul_buku_luar',
        'tanggal_baca',
        'refleksi',
        'poin_diberikan',
        'status_verifikasi',
        'verifikator_id'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_baca' => 'date',
        ];
    }

    public function pesertaLiterasi()
    {
        return $this->belongsTo(PesertaLiterasi::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}
