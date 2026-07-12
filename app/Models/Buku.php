<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Buku extends Model
{
    use SoftDeletes;

    protected $table = 'buku';

    protected $fillable = [
        'isbn',
        'judul',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'edisi',
        'halaman',
        'bahasa',
        'deskripsi',
        'cover_image',
        'is_digital',
        'file_digital',
        'rak_lokasi_id'
    ];

    protected function casts(): array
    {
        return [
            'halaman' => 'integer',
            'is_digital' => 'boolean',
        ];
    }

    public function rakLokasi()
    {
        return $this->belongsTo(RakLokasi::class);
    }

    public function kategori()
    {
        return $this->belongsToMany(Kategori::class, 'buku_kategori');
    }

    public function eksemplar()
    {
        return $this->hasMany(Eksemplar::class);
    }

    // Accessor: Default image if cover is null
    protected function coverUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->cover_image) {
                    return asset('assets/images/default-book.svg');
                }

                $path = preg_replace('#^public/#', '', $this->cover_image);

                return Storage::disk('public')->url($path);
            }
        );
    }
}
