<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DokumenAdministrasi extends Model
{
    use SoftDeletes;

    protected $table = 'dokumen_administrasi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori_dokumen',
        'versi',
        'file_path',
        'tipe_file',
        'ukuran_file',
        'status',
        'masa_berlaku_sampai',
        'user_id',
        'parent_id'
    ];

    protected function casts(): array
    {
        return [
            'masa_berlaku_sampai' => 'date',
        ];
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(DokumenAdministrasi::class, 'parent_id');
    }

    public function history()
    {
        return $this->hasMany(DokumenAdministrasi::class, 'parent_id')->orderBy('created_at', 'desc');
    }

    // Accessor: Get formatted file size
    protected function ukuranFormat(): Attribute
    {
        return Attribute::make(
            get: function () {
                $kb = $this->ukuran_file;
                if ($kb < 1024) return $kb . ' KB';
                return round($kb / 1024, 2) . ' MB';
            }
        );
    }
    
    // Accessor: Human readable category name
    protected function namaKategori(): Attribute
    {
        return Attribute::make(
            get: function () {
                $names = [
                    'sk_kepala' => 'SK Kepala Perpustakaan',
                    'sk_petugas' => 'SK Petugas',
                    'struktur_organisasi' => 'Struktur Organisasi',
                    'program_kerja' => 'Program Kerja Tahunan',
                    'visi_misi' => 'Visi & Misi',
                    'tata_tertib' => 'Tata Tertib',
                    'sop' => 'SOP Layanan',
                    'jadwal_layanan' => 'Jadwal Layanan',
                    'denah_perpustakaan' => 'Denah Perpustakaan'
                ];
                return $names[$this->kategori_dokumen] ?? ucwords(str_replace('_', ' ', $this->kategori_dokumen));
            }
        );
    }
}
