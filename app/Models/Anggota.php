<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Anggota extends Model
{
    use SoftDeletes;

    protected $table = 'anggota';

    protected $fillable = [
        'user_id',
        'nomor_anggota',
        'tipe_anggota',
        'no_identitas',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'status',
        'foto',
        'masa_berlaku_sampai'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'masa_berlaku_sampai' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    // Accessor: Default image if foto is null
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto ? asset('storage/' . $this->foto) : 'https://ui-avatars.com/api/?name='.urlencode($this->nama_lengkap ?? 'Anggota').'&background=4361ee&color=fff'
        );
    }


    public function denda()
    {
        return $this->hasMany(Denda::class);
    }

    // Accessor: Nama lengkap dari tabel User
    protected function namaLengkap(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user ? $this->user->name : '-'
        );
    }
}
