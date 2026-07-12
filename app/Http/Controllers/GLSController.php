<?php

namespace App\Http\Controllers;

use App\Models\ProgramLiterasi;
use App\Models\PesertaLiterasi;
use App\Models\LogBacaan;
use App\Models\DokumentasiLiterasi;
use App\Http\Requests\StoreProgramLiterasiRequest;
use App\Http\Requests\VerifikasiJurnalRequest;
use App\Exports\GLSExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class GLSController extends Controller
{
    // ============================================
    // DASHBOARD & STATISTIK GLS
    // ============================================
    public function dashboard()
    {
        // Statistik Global
        $stats = [
            'total_program' => ProgramLiterasi::count(),
            'total_peserta' => PesertaLiterasi::count(),
            'buku_dibaca' => LogBacaan::where('status_verifikasi', 'disetujui')->count(),
            'jurnal_pending' => LogBacaan::where('status_verifikasi', 'pending')->count(),
        ];

        // Data Grafik (6 Bulan Terakhir Log Bacaan)
        $chartData = LogBacaan::select(DB::raw("DATE_FORMAT(tanggal_baca, '%Y-%m') as bulan"), DB::raw('count(*) as total'))
            ->where('status_verifikasi', 'disetujui')
            ->where('tanggal_baca', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
            
        $labels = [];
        $data = [];
        foreach ($chartData as $row) {
            $labels[] = Carbon::createFromFormat('Y-m', $row->bulan)->format('M Y');
            $data[] = $row->total;
        }

        // Leaderboard Top 5 Siswa
        $leaderboard = PesertaLiterasi::with(['anggota.user', 'programLiterasi'])
            ->orderBy('total_poin', 'desc')
            ->limit(5)
            ->get();

        return view('gls.dashboard', compact('stats', 'labels', 'data', 'leaderboard'));
    }

    // ============================================
    // MANAJEMEN PROGRAM LITERASI
    // ============================================
    public function programIndex(Request $request)
    {
        if ($request->ajax()) {
            $query = ProgramLiterasi::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('periode', function($row) {
                    return $row->periode_mulai->format('d M Y') . ' - ' . $row->periode_selesai->format('d M Y');
                })
                ->addColumn('status_badge', function($row) {
                    if($row->status == 'aktif') return '<span class="badge bg-success">Aktif</span>';
                    if($row->status == 'selesai') return '<span class="badge bg-secondary">Selesai</span>';
                    return '<span class="badge bg-warning text-dark">Draft</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="'.route('gls.program.show', $row->id).'" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-folder-open"></i> Detail</a>';
                    $btn .= '<a href="'.route('gls.program.edit', $row->id).'" class="btn btn-sm btn-warning text-white"><i class="fa-solid fa-pen"></i> Edit</a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('gls.program.index');
    }

    public function programCreate()
    {
        return view('gls.program.form');
    }

    public function programStore(StoreProgramLiterasiRequest $request)
    {
        ProgramLiterasi::create($request->validated());
        return redirect()->route('gls.program.index')->with('success', 'Program literasi berhasil ditambahkan.');
    }

    public function programEdit($id)
    {
        $program = ProgramLiterasi::findOrFail($id);
        return view('gls.program.form', compact('program'));
    }

    public function programUpdate(StoreProgramLiterasiRequest $request, $id)
    {
        $program = ProgramLiterasi::findOrFail($id);
        $program->update($request->validated());
        return redirect()->route('gls.program.index')->with('success', 'Program literasi berhasil diperbarui.');
    }

    public function programShow($id)
    {
        $program = ProgramLiterasi::with(['peserta.anggota.user', 'dokumentasi'])->findOrFail($id);
        return view('gls.program.show', compact('program'));
    }

    // ============================================
    // UPLOAD DOKUMENTASI PROGRAM
    // ============================================
    public function uploadDokumentasi(Request $request, $id)
    {
        $request->validate([
            'tipe_file' => 'required|in:foto,pdf',
            'file' => 'required|file|max:5120', // Max 5MB
            'keterangan' => 'nullable|string|max:255'
        ]);

        $program = ProgramLiterasi::findOrFail($id);
        
        $path = $request->file('file')->store('public/gls_dokumentasi');

        DokumentasiLiterasi::create([
            'program_literasi_id' => $program->id,
            'tipe_file' => $request->tipe_file,
            'file_path' => $path,
            'keterangan' => $request->keterangan
        ]);

        return back()->with('success', 'Dokumentasi berhasil diunggah.');
    }

    // ============================================
    // VERIFIKASI JURNAL BACAAN
    // ============================================
    public function jurnalIndex(Request $request)
    {
        if ($request->ajax()) {
            $query = LogBacaan::with(['pesertaLiterasi.anggota.user', 'pesertaLiterasi.programLiterasi', 'buku']);
            
            if ($request->filled('status')) {
                $query->where('status_verifikasi', $request->status);
            } else {
                // Default tampilkan yang pending saja agar guru fokus
                $query->where('status_verifikasi', 'pending');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('peserta', function($row) {
                    return '<strong>'.$row->pesertaLiterasi->anggota->user->name.'</strong><br><small>'.$row->pesertaLiterasi->programLiterasi->nama_program.'</small>';
                })
                ->addColumn('bacaan', function($row) {
                    $judul = $row->buku ? $row->buku->judul : $row->judul_buku_luar;
                    return '<strong>'.$judul.'</strong><br><small class="text-muted">Tgl Baca: '.$row->tanggal_baca->format('d/m/Y').'</small>';
                })
                ->addColumn('refleksi_text', function($row) {
                    return '<div style="max-height: 80px; overflow-y: auto; font-size: 0.85rem;">'.nl2br(htmlspecialchars($row->refleksi)).'</div>';
                })
                ->addColumn('action', function($row) {
                    if($row->status_verifikasi == 'pending') {
                        $btn = '<button type="button" class="btn btn-sm btn-success btn-verify" data-id="'.$row->id.'" data-action="disetujui"><i class="fa-solid fa-check"></i> Setujui</button> ';
                        $btn .= '<button type="button" class="btn btn-sm btn-danger btn-verify" data-id="'.$row->id.'" data-action="ditolak"><i class="fa-solid fa-times"></i> Tolak</button>';
                        return $btn;
                    }
                    return '<span class="badge bg-secondary">'.ucfirst($row->status_verifikasi).'</span>';
                })
                ->rawColumns(['peserta', 'bacaan', 'refleksi_text', 'action'])
                ->make(true);
        }
        return view('gls.jurnal.index');
    }

    public function verifikasiJurnal(VerifikasiJurnalRequest $request)
    {
        try {
            DB::beginTransaction();
            $log = LogBacaan::with('pesertaLiterasi')->findOrFail($request->log_id);
            
            if ($log->status_verifikasi != 'pending') {
                throw new \Exception('Jurnal ini sudah diverifikasi sebelumnya.');
            }

            $log->update([
                'status_verifikasi' => $request->status_verifikasi,
                'poin_diberikan' => $request->status_verifikasi == 'disetujui' ? $request->poin_diberikan : 0,
                'verifikator_id' => auth()->id()
            ]);

            // Jika disetujui, tambahkan poin ke total peserta
            if ($request->status_verifikasi == 'disetujui') {
                $log->pesertaLiterasi->increment('total_poin', $request->poin_diberikan);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Jurnal berhasil diverifikasi.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ============================================
    // EKSPOR LAPORAN
    // ============================================
    public function exportExcel(Request $request)
    {
        return Excel::download(new GLSExport($request->program_id), 'Laporan_GLS_'.date('Ymd').'.xlsx');
    }
}
