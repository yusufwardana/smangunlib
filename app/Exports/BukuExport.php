<?php

namespace App\Exports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BukuExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Buku::query()->with(['rakLokasi', 'eksemplar']);
        
        if (!empty($this->filters['rak_lokasi_id'])) {
            $query->where('rak_lokasi_id', $this->filters['rak_lokasi_id']);
        }
        if (!empty($this->filters['kategori_id'])) {
            $query->whereHas('kategori', function($q) {
                $q->where('kategori.id', $this->filters['kategori_id']);
            });
        }

        return $query;
    }

    public function map($buku): array
    {
        return [
            $buku->isbn ?? '-',
            $buku->judul,
            $buku->pengarang,
            $buku->penerbit,
            $buku->tahun_terbit,
            $buku->rakLokasi ? $buku->rakLokasi->nama_lokasi : '-',
            $buku->is_digital ? 'Ya' : 'Tidak',
            $buku->eksemplar->count()
        ];
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'Judul Buku',
            'Pengarang',
            'Penerbit',
            'Tahun Terbit',
            'Rak Lokasi',
            'E-Book',
            'Jumlah Eksemplar Fisik'
        ];
    }
}
