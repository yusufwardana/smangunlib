<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use SoftDeletes;

    protected $table = 'peminjaman';

    protected $fillable = [
        'nomor_transaksi',
        'anggota_id',
        'user_id',
        'tanggal_pinjam',
        'due_date',
        'status',
        'perpanjangan_count',
        'keterangan'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'due_date' => 'date',
            'perpanjangan_count' => 'integer',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function pustakawan()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
