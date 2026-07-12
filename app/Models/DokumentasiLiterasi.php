<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DokumentasiLiterasi extends Model
{
    protected $table = 'dokumentasi_literasi';

    protected $fillable = [
        'program_literasi_id',
        'tipe_file',
        'file_path',
        'keterangan',
    ];

    public function programLiterasi()
    {
        return $this->belongsTo(ProgramLiterasi::class);
    }

    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => asset('storage/' . $this->file_path)
        );
    }
}
