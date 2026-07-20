<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User;
use App\Http\Requests\StoreAnggotaRequest;
use App\Http\Requests\UpdateAnggotaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\AnggotaExport;
use Maatwebsite\Excel\Facades\Excel;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Anggota::with('user');

            if ($request->filled('tipe_anggota')) {
                $query->where('tipe_anggota', $request->tipe_anggota);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('foto_profil', function($row) {
                    return '<img src="'.$row->foto_url.'" alt="Foto" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">';
                })
                ->addColumn('nama_lengkap', function($row) {
                    return '<strong>'.($row->user->name ?? '-').'</strong><br><small class="text-muted">'.$row->nomor_anggota.'</small>';
                })
                ->addColumn('status_badge', function($row) {
                    if($row->status == 'aktif') return '<span class="badge bg-success-subtle text-success px-3 rounded-pill">Aktif</span>';
                    if($row->status == 'non-aktif') return '<span class="badge bg-secondary-subtle text-secondary px-3 rounded-pill">Nonaktif</span>';
                    return '<span class="badge bg-danger-subtle text-danger px-3 rounded-pill">Blacklist</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="'.route('anggota.show', $row->id).'" class="btn btn-sm btn-info text-white" title="Detail"><i class="fa-solid fa-eye"></i></a>';
                    $btn .= '<a href="'.route('anggota.edit', $row->id).'" class="btn btn-sm btn-warning text-white" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'" title="Hapus"><i class="fa-solid fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['foto_profil', 'nama_lengkap', 'status_badge', 'action'])
                ->make(true);
        }

        return view('anggota.index');
    }

    public function create()
    {
        return view('anggota.form');
    }

    public function store(StoreAnggotaRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            // Generate User Account automatically
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'smangunlib123') // Default password
            ]);
            
            // Assign role based on tipe_anggota
            $roleName = $data['tipe_anggota'];
            if ($roleName === 'tendik') {
                $roleName = 'pustakawan';
            }
            if (\Spatie\Permission\Models\Role::where('name', $roleName)->exists()) {
                $user->assignRole($roleName);
            }

            // Upload Foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('public/foto_anggota');
            }

            // Generate Member Number (contoh format: SMAN-Tahun-Urutan)
            $lastAnggota = Anggota::latest('id')->first();
            $nextId = $lastAnggota ? $lastAnggota->id + 1 : 1;
            $nomorAnggota = 'SMAN-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Create Anggota Profile
            Anggota::create([
                'user_id' => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'tipe_anggota' => $data['tipe_anggota'],
                'no_identitas' => $data['no_identitas'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'alamat' => $data['alamat'],
                'no_telepon' => $data['no_telepon'],
                'status' => $data['status'],
                'foto' => $fotoPath,
                'masa_berlaku_sampai' => now()->addYears(3), // Default 3 tahun
            ]);
        });

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan dan akun login dibuat.');
    }

    public function show($id)
    {
        $anggota = Anggota::with(['user', 'peminjaman.detailPeminjaman.eksemplar.buku'])->findOrFail($id);
        return view('anggota.show', compact('anggota'));
    }

    public function edit($id)
    {
        $anggota = Anggota::with('user')->findOrFail($id);
        return view('anggota.form', compact('anggota'));
    }

    public function update(UpdateAnggotaRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $anggota = Anggota::findOrFail($id);
            $data = $request->validated();
            
            // Update User Account
            if ($anggota->user) {
                $userData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ];
                if (!empty($data['password'])) {
                    $userData['password'] = Hash::make($data['password']);
                }
                $anggota->user->update($userData);
                
                // Sync Role
                $roleName = $data['tipe_anggota'];
                if ($roleName === 'tendik') {
                    $roleName = 'pustakawan';
                }
                if (\Spatie\Permission\Models\Role::where('name', $roleName)->exists()) {
                    $anggota->user->syncRoles([$roleName]);
                }
            }

            // Update Foto
            $fotoPath = $anggota->foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath && Storage::exists($fotoPath)) {
                    Storage::delete($fotoPath);
                }
                $fotoPath = $request->file('foto')->store('public/foto_anggota');
            }

            // Update Anggota Profile
            $anggota->update([
                'tipe_anggota' => $data['tipe_anggota'],
                'no_identitas' => $data['no_identitas'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'alamat' => $data['alamat'],
                'no_telepon' => $data['no_telepon'],
                'status' => $data['status'],
                'foto' => $fotoPath,
            ]);
        });

        return redirect()->route('anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete(); // Soft delete for Anggota. User account is kept but can't login if we implement active check.
        return response()->json(['success' => true, 'message' => 'Anggota berhasil dinonaktifkan / dihapus.']);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new AnggotaExport($request->all()), 'Data_Anggota_'.date('Ymd').'.xlsx');
    }

    public function printKartuMassal(Request $request)
    {
        $ids = explode(',', $request->ids);
        $anggotas = Anggota::whereIn('id', $ids)->with('user')->get();
        return view('anggota.print_kartu', compact('anggotas'));
    }
}
