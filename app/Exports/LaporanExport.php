<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class LaporanExport implements FromView
{
    use Exportable;

    protected $tipe;
    protected $data;
    protected $judulLaporan;
    protected $start;
    protected $end;

    public function __construct($tipe, $data, $judulLaporan, $start, $end)
    {
        $this->tipe = $tipe;
        $this->data = $data;
        $this->judulLaporan = $judulLaporan;
        $this->start = $start;
        $this->end = $end;
    }

    public function view(): View
    {
        return view('laporan.print', [
            'data' => $this->data,
            'tipe' => $this->tipe,
            'judulLaporan' => $this->judulLaporan,
            'start' => $this->start,
            'end' => $this->end,
            'isExcel' => true
        ]);
    }
}
