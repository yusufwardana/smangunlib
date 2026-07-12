<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Eksemplar;
use App\Models\Anggota;
use App\Models\Denda;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\ProsesPengembalianRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class SirkulasiController extends Controller
{
    private $tarifDenda = 1000; // Rp 1.000 per hari per buku

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Peminjaman::with(['anggota.user', 'pustakawan']);

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
                $query->whereBetween('tanggal_pinjam', [$request->tanggal_dari, $request->tanggal_sampai]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('peminjam', function($row) {
                    return '<strong>'.($row->anggota->user->name ?? '-').'</strong><br><small>'.$row->anggota->nomor_anggota.'</small>';
                })
                ->addColumn('tanggal', function($row) {
                    return 'Pinjam: ' . $row->tanggal_pinjam->format('d/m/Y') . '<br>Tempo: <span class="text-danger">' . $row->due_date->format('d/m/Y') . '</span>';
                })
                ->addColumn('status_badge', function($row) {
                    if ($row->status == 'dipinjam') {
                        // Check if overdue
                        if (Carbon::now()->startOfDay()->gt($row->due_date)) {
                            return '<span class="badge bg-danger-subtle text-danger rounded-pill px-3">Terlambat</span>';
                        }
                        return '<span class="badge bg-primary-subtle text-primary rounded-pill px-3">Dipinjam</span>';
                    }
                    if ($row->status == 'dikembalikan') return '<span class="badge bg-success-subtle text-success rounded-pill px-3">Selesai</span>';
                    return '<span class="badge bg-warning-subtle text-warning rounded-pill px-3">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group">';
                    if ($row->status == 'dipinjam') {
                        $btn .= '<a href="'.route('sirkulasi.pengembalian.form', $row->nomor_transaksi).'" class="btn btn-sm btn-success text-white" title="Proses Pengembalian"><i class="fa-solid fa-right-left"></i> Kembali</a>';
                        if ($row->perpanjangan_count < 1 && Carbon::now()->startOfDay()->lte($row->due_date)) {
                            $btn .= '<button type="button" class="btn btn-sm btn-warning text-white btn-perpanjang" data-id="'.$row->id.'" title="Perpanjang"><i class="fa-solid fa-clock-rotate-left"></i></button>';
                        }
                    }
                    $btn .= '<a href="'.route('sirkulasi.cetak_struk', $row->id).'" target="_blank" class="btn btn-sm btn-secondary text-white" title="Cetak Bukti"><i class="fa-solid fa-print"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['peminjam', 'tanggal', 'status_badge', 'action'])
                ->make(true);
        }

        // Dashboard Stats
        $stats = [
            'sedang_dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'terlambat' => Peminjaman::where('status', 'dipinjam')->where('due_date', '<', Carbon::now()->startOfDay())->count(),
            'total_transaksi' => Peminjaman::count(),
            'denda_belum_dibayar' => Denda::where('status_pembayaran', 'belum_lunas')->sum('total_denda')
        ];

        return view('sirkulasi.index', compact('stats'));
    }

    public function peminjamanForm()
    {
        // Untuk dropdown anggota aktif
        $anggotas = Anggota::with('user')->where('status', 'aktif')->get();
        // Untuk pencarian buku/eksemplar yang tersedia
        $eksemplars = Eksemplar::with('buku')->where('status_sirkulasi', 'tersedia')->get();
        
        return view('sirkulasi.peminjaman', compact('anggotas', 'eksemplars'));
    }

    public function storePeminjaman(StorePeminjamanRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $anggota = Anggota::findOrFail($request->anggota_id);
            
            // Cek limit peminjaman (misal max 3 buku)
            $sedangDipinjam = Peminjaman::where('anggota_id', $anggota->id)->where('status', 'dipinjam')->count();
            if ($sedangDipinjam + count($request->eksemplar_ids) > 3) {
                return back()->with('error', 'Anggota ini melebihi limit peminjaman maksimal (3 buku).');
            }

            // Cek apakah ada denda belum lunas
            $dendaTertunggak = Denda::where('anggota_id', $anggota->id)->where('status_pembayaran', 'belum_lunas')->exists();
            if ($dendaTertunggak) {
                return back()->with('error', 'Anggota masih memiliki denda yang belum dilunasi.');
            }

            // Create Transaksi
            $nomorTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            $peminjaman = Peminjaman::create([
                'nomor_transaksi' => $nomorTransaksi,
                'anggota_id' => $anggota->id,
                'user_id' => auth()->id(), // Pustakawan
                'tanggal_pinjam' => Carbon::today(),
                'due_date' => Carbon::today()->addDays(7), // Default pinjam 7 hari
                'status' => 'dipinjam',
                'keterangan' => $request->keterangan
            ]);

            foreach ($request->eksemplar_ids as $eksId) {
                $eksemplar = Eksemplar::where('id', $eksId)->where('status_sirkulasi', 'tersedia')->lockForUpdate()->first();
                if (!$eksemplar) {
                    throw new \Exception('Salah satu buku tidak tersedia / sudah dipinjam.');
                }

                // Create Detail
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'eksemplar_id' => $eksemplar->id,
                    'status' => 'dipinjam'
                ]);

                // Update Eksemplar Stok
                $eksemplar->update(['status_sirkulasi' => 'dipinjam']);
            }

            DB::commit();
            return redirect()->route('sirkulasi.cetak_struk', $peminjaman->id)->with('success', 'Transaksi berhasil.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function pengembalianForm($nomor_transaksi = null)
    {
        $peminjaman = null;
        if ($nomor_transaksi) {
            $peminjaman = Peminjaman::with(['anggota.user', 'detailPeminjaman.eksemplar.buku'])
                ->where('nomor_transaksi', $nomor_transaksi)
                ->where('status', 'dipinjam')
                ->first();
        }
        return view('sirkulasi.pengembalian', compact('peminjaman', 'nomor_transaksi'));
    }

    public function prosesPengembalian(ProsesPengembalianRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $peminjaman = Peminjaman::with('detailPeminjaman.eksemplar')->findOrFail($id);
            if ($peminjaman->status != 'dipinjam') throw new \Exception('Transaksi sudah ditutup.');

            $semuaSelesai = true;
            $hariTerlambat = Carbon::now()->startOfDay()->diffInDays($peminjaman->due_date, false);
            // diffInDays false: if now is AFTER due_date, it returns negative.
            $keterlambatan = $hariTerlambat < 0 ? abs($hariTerlambat) : 0;

            foreach ($request->detail as $detailId => $input) {
                $detail = DetailPeminjaman::findOrFail($detailId);
                $eksemplar = $detail->eksemplar;

                if (isset($input['kembalikan']) && $input['kembalikan'] == 1) {
                    
                    $kondisi = $input['kondisi_kembali']; // baik, rusak, hilang
                    
                    // Update Detail
                    $detail->update([
                        'tanggal_kembali' => Carbon::today(),
                        'kondisi_kembali' => $kondisi,
                        'status' => 'dikembalikan'
                    ]);

                    // Update Eksemplar Status
                    if ($kondisi == 'baik') {
                        $eksemplar->update(['status_sirkulasi' => 'tersedia']);
                    } else {
                        $eksemplar->update(['status_sirkulasi' => $kondisi]); // rusak / hilang
                    }

                    // Logika Denda
                    $dendaTotal = 0;
                    if ($keterlambatan > 0) {
                        $dendaTotal += ($keterlambatan * $this->tarifDenda);
                    }
                    if ($kondisi == 'hilang') {
                        $dendaTotal += ($eksemplar->harga ?? 50000); // Denda harga buku jika ada
                    }

                    if ($dendaTotal > 0) {
                        Denda::create([
                            'detail_peminjaman_id' => $detail->id,
                            'anggota_id' => $peminjaman->anggota_id,
                            'jumlah_hari_terlambat' => $keterlambatan,
                            'tarif_per_hari' => $this->tarifDenda,
                            'total_denda' => $dendaTotal,
                            'status_pembayaran' => 'belum_lunas',
                            'user_id' => auth()->id() // Petugas yg mencatat
                        ]);
                    }

                } else {
                    $semuaSelesai = false; // Ada buku yg belum diceklis kembalikan
                }
            }

            if ($semuaSelesai) {
                $peminjaman->update(['status' => 'dikembalikan']);
            }

            DB::commit();
            return redirect()->route('sirkulasi.index')->with('success', 'Buku berhasil dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }

    public function perpanjang($id)
    {
        try {
            DB::beginTransaction();
            $peminjaman = Peminjaman::findOrFail($id);
            
            if ($peminjaman->perpanjangan_count >= 1) {
                throw new \Exception('Batas maksimal perpanjangan sudah habis (1x).');
            }
            if (Carbon::now()->startOfDay()->gt($peminjaman->due_date)) {
                throw new \Exception('Buku sudah terlambat, tidak dapat diperpanjang. Lakukan pengembalian dan bayar denda.');
            }

            $peminjaman->update([
                'due_date' => Carbon::parse($peminjaman->due_date)->addDays(3),
                'perpanjangan_count' => $peminjaman->perpanjangan_count + 1
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Durasi pinjaman diperpanjang +3 hari.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cetakStruk($id)
    {
        $peminjaman = Peminjaman::with(['anggota.user', 'detailPeminjaman.eksemplar.buku', 'pustakawan'])->findOrFail($id);
        return view('sirkulasi.cetak_struk', compact('peminjaman'));
    }
}
