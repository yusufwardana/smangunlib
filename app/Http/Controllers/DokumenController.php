<?php

namespace App\Http\Controllers;

use App\Models\DokumenAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Hanya tampilkan versi terbaru (tidak punya anak / yang bukan arsip history)
            // Asumsi: parent_id = null (Dokumen Utama). History child akan tersembunyi dari Index.
            $query = DokumenAdministrasi::whereNull('parent_id')->with('uploader');

            if ($request->filled('kategori')) {
                $query->where('kategori_dokumen', $request->kategori);
            }
            if ($request->filled('status')) {
                if ($request->status == 'expired') {
                    $query->where('masa_berlaku_sampai', '<', Carbon::today())->where('status', 'aktif');
                } else {
                    $query->where('status', $request->status);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('file_info', function($row) {
                    $icon = $row->tipe_file == 'pdf' ? '<i class="fa-solid fa-file-pdf text-danger fs-3"></i>' : '<i class="fa-solid fa-file-image text-primary fs-3"></i>';
                    return '<div class="d-flex align-items-center gap-2">'.$icon.'
                            <div><strong>'.$row->judul.'</strong><br>
                            <small class="badge bg-secondary">'.$row->versi.'</small> <small class="text-muted">('.$row->ukuran_format.')</small></div></div>';
                })
                ->addColumn('kategori_dokumen', function($row) {
                    return str_replace('_', ' ', strtoupper($row->kategori_dokumen));
                })
                ->addColumn('status_badge', function($row) {
                    if ($row->status == 'arsip') return '<span class="badge bg-secondary">Arsip</span>';
                    
                    if ($row->masa_berlaku_sampai && Carbon::today()->gt($row->masa_berlaku_sampai)) {
                        return '<span class="badge bg-danger">Expired</span>';
                    }
                    return '<span class="badge bg-success">Aktif</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="'.route('dokumen.show', $row->id).'" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-folder-open"></i> Detail & Histori</a>';
                    $btn .= '<a href="'.route('dokumen.edit', $row->id).'" class="btn btn-sm btn-warning text-white" title="Revisi Baru"><i class="fa-solid fa-pen"></i> Update Versi</a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['file_info', 'status_badge', 'action'])
                ->make(true);
        }

        return view('dokumen.index');
    }

    public function create()
    {
        return view('dokumen.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_dokumen' => 'required|string',
            'versi' => 'required|string|max:50',
            'masa_berlaku_sampai' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:10240', // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('private/dokumen'); // Private storage

        DokumenAdministrasi::create([
            'judul' => $request->judul,
            'kategori_dokumen' => $request->kategori_dokumen,
            'versi' => $request->versi,
            'masa_berlaku_sampai' => $request->masa_berlaku_sampai,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'tipe_file' => $file->getClientOriginalExtension(),
            'ukuran_file' => round($file->getSize() / 1024),
            'status' => 'aktif',
            'user_id' => auth()->id()
        ]);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    public function edit($id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        return view('dokumen.form', compact('dokumen'));
    }

    public function update(Request $request, $id)
    {
        // Fitur Versioning: Saat update, kita tidak merubah file lama. Kita arsipkan yg lama, bikin yg baru menimpa ID.
        // Wait, the easier standard way:
        // Update the current record's details. If a NEW file is uploaded, we clone the CURRENT details to a new child record (Archive), 
        // then update the MAIN record with the new file and new version.
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_dokumen' => 'required|string',
            'versi' => 'required|string|max:50',
            'masa_berlaku_sampai' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
        ]);

        try {
            DB::beginTransaction();
            $dokumenUtama = DokumenAdministrasi::findOrFail($id);

            // Jika ada file baru diunggah -> Trigger Versioning
            if ($request->hasFile('file')) {
                // 1. Buat backup arsip dari dokumenUtama
                DokumenAdministrasi::create([
                    'judul' => $dokumenUtama->judul,
                    'kategori_dokumen' => $dokumenUtama->kategori_dokumen,
                    'versi' => $dokumenUtama->versi,
                    'masa_berlaku_sampai' => $dokumenUtama->masa_berlaku_sampai,
                    'deskripsi' => $dokumenUtama->deskripsi,
                    'file_path' => $dokumenUtama->file_path,
                    'tipe_file' => $dokumenUtama->tipe_file,
                    'ukuran_file' => $dokumenUtama->ukuran_file,
                    'status' => 'arsip',
                    'user_id' => $dokumenUtama->user_id,
                    'parent_id' => $dokumenUtama->id,
                    'created_at' => $dokumenUtama->created_at // Pertahankan tgl asli versi itu
                ]);

                // 2. Upload file baru
                $file = $request->file('file');
                $path = $file->store('private/dokumen');
                
                // 3. Update dokumen utama
                $dokumenUtama->file_path = $path;
                $dokumenUtama->tipe_file = $file->getClientOriginalExtension();
                $dokumenUtama->ukuran_file = round($file->getSize() / 1024);
                $dokumenUtama->user_id = auth()->id();
            }

            // Update Meta Data (Terlepas upload file atau cuma ganti teks)
            $dokumenUtama->update([
                'judul' => $request->judul,
                'kategori_dokumen' => $request->kategori_dokumen,
                'versi' => $request->versi,
                'masa_berlaku_sampai' => $request->masa_berlaku_sampai,
                'deskripsi' => $request->deskripsi,
            ]);

            DB::commit();
            return redirect()->route('dokumen.show', $dokumenUtama->id)->with('success', 'Versi Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $dokumen = DokumenAdministrasi::with(['uploader', 'history.uploader'])->findOrFail($id);
        return view('dokumen.show', compact('dokumen'));
    }

    public function download($id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        
        if (!Storage::exists($dokumen->file_path)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        return Storage::download($dokumen->file_path, $dokumen->judul . ' - ' . $dokumen->versi . '.' . $dokumen->tipe_file);
    }
    
    public function preview($id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        
        if (!Storage::exists($dokumen->file_path)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        $file = Storage::get($dokumen->file_path);
        $type = Storage::mimeType($dokumen->file_path);

        return response($file, 200)->header("Content-Type", $type);
    }
}
