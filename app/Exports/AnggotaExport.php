<?php

namespace App\Exports;

use App\Models\Anggota;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AnggotaExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Anggota::query()->with('user');
        
        if (!empty($this->filters['tipe_anggota'])) {
            $query->where('tipe_anggota', $this->filters['tipe_anggota']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query;
    }

    public function map($anggota): array
    {
        return [
            $anggota->nomor_anggota,
            $anggota->user->name ?? '-',
            ucfirst($anggota->tipe_anggota),
            $anggota->no_identitas,
            $anggota->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $anggota->tempat_lahir . ', ' . $anggota->tanggal_lahir->format('d/m/Y'),
            $anggota->no_telepon,
            ucfirst($anggota->status)
        ];
    }

    public function headings(): array
    {
        return [
            'Nomor Anggota',
            'Nama Lengkap',
            'Tipe Anggota',
            'Identitas (NIS/NIP)',
            'Jenis Kelamin',
            'Tempat & Tanggal Lahir',
            'No Telepon',
            'Status'
        ];
    }
}
