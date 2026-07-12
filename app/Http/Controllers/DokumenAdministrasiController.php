<?php

namespace App\Http\Controllers;

use App\Models\DokumenAdministrasi;
use App\Http\Requests\StoreDokumenRequest;
use App\Http\Requests\UpdateDokumenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DokumenAdministrasiController extends Controller
{
    /**
     * Kategori valid yang dizinkan
     */
    private $kategoriValid = [
        'sk_kepala', 'sk_petugas', 'struktur_organisasi', 
        'program_kerja', 'visi_misi', 'tata_tertib', 
        'sop', 'jadwal_layanan', 'denah_perpustakaan'
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $kategori)
    {
        if (!in_array($kategori, $this->kategoriValid)) {
            abort(404, 'Kategori dokumen tidak valid.');
        }

        if ($request->ajax()) {
            $data = DokumenAdministrasi::where('kategori_dokumen', $kategori)
                        ->with('uploader')
                        ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('file', function($row) {
                    $icon = $row->tipe_file == 'pdf' ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary';
                    return '<i class="fa-solid ' . $icon . ' fs-5"></i> ' . strtoupper($row->tipe_file);
                })
                ->addColumn('uploader_name', function($row) {
                    return $row->uploader ? $row->uploader->name : '-';
                })
                ->addColumn('action', function($row) use ($kategori) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="'.route('administrasi.show', ['kategori' => $kategori, 'dokuman' => $row->id]).'" class="btn btn-sm btn-info text-white" title="Preview"><i class="fa-solid fa-eye"></i></a>';
                    $btn .= '<a href="'.route('administrasi.edit', ['kategori' => $kategori, 'dokuman' => $row->id]).'" class="btn btn-sm btn-warning text-white" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'" title="Hapus"><i class="fa-solid fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['file', 'action'])
                ->make(true);
        }

        return view('administrasi.index', compact('kategori'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $kategori)
    {
        if (!in_array($kategori, $this->kategoriValid)) abort(404);
        return view('administrasi.form', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDokumenRequest $request, string $kategori)
    {
        $file = $request->file('file_dokumen');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('private/administrasi/' . $kategori, $fileName);

        DokumenAdministrasi::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori_dokumen' => $kategori,
            'file_path' => $path,
            'tipe_file' => strtolower($file->getClientOriginalExtension()),
            'ukuran_file' => round($file->getSize() / 1024), // KB
            'status' => $request->status,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('administrasi.index', $kategori)
                         ->with('success', 'Dokumen berhasil diunggah.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kategori, string $id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        
        if ($dokumen->kategori_dokumen !== $kategori) abort(404);

        return view('administrasi.show', compact('dokumen', 'kategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $kategori, string $id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        return view('administrasi.form', compact('kategori', 'dokumen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDokumenRequest $request, string $kategori, string $id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        
        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ];

        if ($request->hasFile('file_dokumen')) {
            // Delete old file
            if (Storage::exists($dokumen->file_path)) {
                Storage::delete($dokumen->file_path);
            }

            $file = $request->file('file_dokumen');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('private/administrasi/' . $kategori, $fileName);

            $data['file_path'] = $path;
            $data['tipe_file'] = strtolower($file->getClientOriginalExtension());
            $data['ukuran_file'] = round($file->getSize() / 1024);
        }

        $dokumen->update($data);

        return redirect()->route('administrasi.index', $kategori)
                         ->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kategori, string $id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        $dokumen->delete(); // Soft delete

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus.']);
    }

    /**
     * Download the document.
     */
    public function download(string $kategori, string $id)
    {
        $dokumen = DokumenAdministrasi::findOrFail($id);
        if (!Storage::exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }
        return Storage::download($dokumen->file_path, $dokumen->judul . '.' . $dokumen->tipe_file);
    }
}
