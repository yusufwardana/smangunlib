<?php

namespace App\Http\Controllers\Koleksi;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\RakLokasi;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\BukuExport;
use Maatwebsite\Excel\Facades\Excel;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Buku::with(['rakLokasi', 'kategori', 'eksemplar']);

            // Filter Kategori
            if ($request->filled('kategori_id')) {
                $query->whereHas('kategori', function($q) use ($request) {
                    $q->where('kategori.id', $request->kategori_id);
                });
            }

            // Filter Rak
            if ($request->filled('rak_lokasi_id')) {
                $query->where('rak_lokasi_id', $request->rak_lokasi_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('cover', function($row) {
                    return '<img src="'.$row->cover_url.'" alt="Cover" class="img-thumbnail" style="width: 50px; height: 70px; object-fit: cover;">';
                })
                ->addColumn('judul_buku', function($row) {
                    $badge = $row->is_digital ? '<span class="badge bg-info-subtle text-info ms-2">E-Book</span>' : '';
                    return '<strong>'.$row->judul.'</strong>' . $badge . '<br><small class="text-muted">'.$row->pengarang.'</small>';
                })
                ->addColumn('stok', function($row) {
                    $tersedia = $row->eksemplar->where('status_sirkulasi', 'tersedia')->count();
                    $total = $row->eksemplar->count();
                    return $tersedia . ' / ' . $total;
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="'.route('koleksi.buku.show', $row->id).'" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-eye"></i></a>';
                    $btn .= '<a href="'.route('koleksi.buku.edit', $row->id).'" class="btn btn-sm btn-warning text-white"><i class="fa-solid fa-pen-to-square"></i></a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"><i class="fa-solid fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['cover', 'judul_buku', 'action'])
                ->make(true);
        }

        $kategoris = Kategori::all();
        $raks = RakLokasi::all();
        
        return view('koleksi.buku.index', compact('kategoris', 'raks'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        $raks = RakLokasi::all();
        return view('koleksi.buku.form', compact('kategoris', 'raks'));
    }

    public function store(StoreBukuRequest $request)
    {
        $data = $request->validated();
        
        $data['is_digital'] = $request->has('is_digital');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('file_digital') && $data['is_digital']) {
            $data['file_digital'] = $request->file('file_digital')->store('private/ebooks');
        }

        $buku = Buku::create($data);

        if ($request->has('kategori_ids')) {
            $buku->kategori()->sync($request->kategori_ids);
        }

        return redirect()->route('koleksi.buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show($id)
    {
        $buku = Buku::with(['rakLokasi', 'kategori', 'eksemplar'])->findOrFail($id);
        return view('koleksi.buku.show', compact('buku'));
    }

    public function edit($id)
    {
        $buku = Buku::with('kategori')->findOrFail($id);
        $kategoris = Kategori::all();
        $raks = RakLokasi::all();
        return view('koleksi.buku.form', compact('buku', 'kategoris', 'raks'));
    }

    public function update(UpdateBukuRequest $request, $id)
    {
        $buku = Buku::findOrFail($id);
        $data = $request->validated();
        
        $data['is_digital'] = $request->has('is_digital');

        if ($request->hasFile('cover_image')) {
            if ($buku->cover_image && Storage::exists($buku->cover_image)) {
                Storage::delete($buku->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('file_digital') && $data['is_digital']) {
            if ($buku->file_digital && Storage::exists($buku->file_digital)) {
                Storage::delete($buku->file_digital);
            }
            $data['file_digital'] = $request->file('file_digital')->store('private/ebooks');
        }

        $buku->update($data);

        if ($request->has('kategori_ids')) {
            $buku->kategori()->sync($request->kategori_ids);
        }

        return redirect()->route('koleksi.buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete(); // Soft delete
        return response()->json(['success' => true, 'message' => 'Buku berhasil dihapus.']);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new BukuExport($request->all()), 'Katalog_Buku_'.date('Ymd').'.xlsx');
    }

    public function printBarcodeMassal(Request $request)
    {
        // Fitur cetak barcode massal dari beberapa ID buku yang dipilih
        // Menggunakan library Milon/Barcode di Blade View
        $ids = explode(',', $request->ids);
        $bukus = Buku::whereIn('id', $ids)->with('eksemplar')->get();
        return view('koleksi.buku.print_barcode', compact('bukus'));
    }
}
