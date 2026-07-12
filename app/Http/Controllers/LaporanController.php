<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

// Models
use App\Models\Buku;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\BukuTamu;
use App\Models\Inventaris;
use App\Models\ProgramLiterasi;
use App\Models\Denda;
use App\Models\DokumenAdministrasi;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function generate(Request $request)
    {
        $tipe = $request->tipe_laporan;
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;
        $kategori = $request->kategori ?? null;

        $data = [];
        $chartData = [];
        $judulLaporan = "Laporan";

        switch ($tipe) {
            case 'koleksi':
                $judulLaporan = "Laporan Koleksi Buku";
                $query = Buku::withCount('eksemplar');
                if ($kategori) $query->where('kategori_id', $kategori);
                if ($start && $end) $query->whereBetween('created_at', [$start, $end]);
                $data = $query->get();
                break;
                
            case 'anggota':
                $judulLaporan = "Laporan Anggota Perpustakaan";
                $query = Anggota::with('user');
                if ($kategori) $query->where('tipe_anggota', $kategori);
                if ($start && $end) $query->whereBetween('created_at', [$start, $end]);
                $data = $query->get();
                break;

            case 'peminjaman':
                $judulLaporan = "Laporan Peminjaman Buku";
                $query = Peminjaman::with(['anggota.user']);
                if ($start && $end) $query->whereBetween('tanggal_pinjam', [$start, $end]);
                $data = $query->get();
                break;
                
            case 'denda':
                $judulLaporan = "Laporan Denda Sirkulasi";
                $query = Denda::with(['peminjaman.anggota.user']);
                if ($kategori) $query->where('status_pembayaran', $kategori); // lunas/belum_lunas
                if ($start && $end) $query->whereBetween('created_at', [$start, $end]);
                $data = $query->get();
                break;

            case 'pengunjung':
                $judulLaporan = "Laporan Pengunjung (Buku Tamu)";
                $query = BukuTamu::query();
                if ($kategori) $query->where('tipe_pengunjung', $kategori);
                if ($start && $end) $query->whereBetween('tanggal_kunjungan', [$start, $end]);
                $data = $query->get();
                break;

            case 'inventaris':
            case 'sarana_prasarana':
                $judulLaporan = "Laporan Inventaris & Sarana Prasarana";
                $query = Inventaris::query();
                if ($kategori) $query->where('kondisi', $kategori); // baik/rusak dll
                $data = $query->get(); // Biasanya inventaris tidak difilter tgl karena aset berjalan
                break;
                
            case 'literasi':
                $judulLaporan = "Laporan Program Literasi (GLS)";
                $query = ProgramLiterasi::withCount(['peserta', 'dokumentasi']);
                if ($kategori) $query->where('status', $kategori);
                if ($start && $end) $query->whereBetween('periode_mulai', [$start, $end]);
                $data = $query->get();
                break;

            case 'evaluasi':
                $judulLaporan = "Laporan Evaluasi & Dokumen";
                $query = DokumenAdministrasi::where('kategori_dokumen', 'Evaluasi')->with('uploader');
                if ($start && $end) $query->whereBetween('created_at', [$start, $end]);
                $data = $query->get();
                break;
        }

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new LaporanExport($tipe, $data, $judulLaporan, $start, $end), str_replace(' ', '_', $judulLaporan) . '.xlsx');
            }
            if ($request->export == 'pdf' || $request->export == 'print') {
                return view('laporan.print', compact('data', 'tipe', 'judulLaporan', 'start', 'end'));
            }
        }

        return view('laporan.hasil', compact('data', 'tipe', 'judulLaporan', 'start', 'end'));
    }
}
