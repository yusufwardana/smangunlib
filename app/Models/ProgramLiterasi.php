<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramLiterasi extends Model
{
    use SoftDeletes;

    protected $table = 'program_literasi';

    protected $fillable = [
        'nama_program',
        'periode_mulai',
        'periode_selesai',
        'deskripsi',
        'target_baca',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
            'target_baca' => 'integer',
        ];
    }


    public function peserta()
    {
        return $this->hasMany(PesertaLiterasi::class);
    }

    public function dokumentasi()
    {
        return $this->hasMany(DokumentasiLiterasi::class);
    }
}
