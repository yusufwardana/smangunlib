<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaLiterasi extends Model
{
    protected $table = 'peserta_literasi';

    protected $fillable = [
        'program_literasi_id',
        'anggota_id',
        'total_poin',
        'status',
        'tanggal_daftar'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'datetime',
        ];
    }

    public function programLiterasi()
    {
        return $this->belongsTo(ProgramLiterasi::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function logBacaan()
    {
        return $this->hasMany(LogBacaan::class);
    }
}
