<?php

namespace App\Exports;

use App\Models\PesertaLiterasi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GLSExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $program_id;

    public function __construct($program_id)
    {
        $this->program_id = $program_id;
    }

    public function query()
    {
        return PesertaLiterasi::query()
            ->with(['anggota.user'])
            ->where('program_literasi_id', $this->program_id)
            ->orderByDesc('total_poin');
    }

    public function map($peserta): array
    {
        return [
            $peserta->anggota->nomor_anggota,
            $peserta->anggota->user->name ?? '-',
            ucfirst($peserta->anggota->tipe_anggota),
            $peserta->tanggal_daftar->format('d/m/Y'),
            $peserta->total_poin,
            ucfirst($peserta->status)
        ];
    }

    public function headings(): array
    {
        return [
            'Nomor Anggota',
            'Nama Lengkap',
            'Tipe',
            'Tanggal Mendaftar',
            'Total Poin',
            'Status'
        ];
    }
}
