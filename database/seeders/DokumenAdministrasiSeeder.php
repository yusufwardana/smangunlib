<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DokumenAdministrasi;
use App\Models\User;

class DokumenAdministrasiSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first(); // Asumsi Super Admin sudah ada

        DokumenAdministrasi::create([
            'judul' => 'SK Kepala Perpustakaan 2026/2027',
            'deskripsi' => 'SK Pengangkatan Kepala Perpustakaan Tahun Ajaran Baru',
            'kategori_dokumen' => 'sk_kepala',
            'file_path' => 'private/administrasi/sk_kepala/sample_sk_kepala.pdf',
            'tipe_file' => 'pdf',
            'ukuran_file' => 1250,
            'status' => 'aktif',
            'user_id' => $admin->id ?? null,
        ]);

        DokumenAdministrasi::create([
            'judul' => 'SOP Layanan Sirkulasi 2026',
            'deskripsi' => 'Standar Operasional Prosedur Peminjaman dan Pengembalian',
            'kategori_dokumen' => 'sop',
            'file_path' => 'private/administrasi/sop/sample_sop.pdf',
            'tipe_file' => 'pdf',
            'ukuran_file' => 840,
            'status' => 'aktif',
            'user_id' => $admin->id ?? null,
        ]);
        
        DokumenAdministrasi::create([
            'judul' => 'Denah Ruang Perpustakaan Lantai 1',
            'deskripsi' => 'Denah lengkap letak rak buku kelas X dan XI',
            'kategori_dokumen' => 'denah_perpustakaan',
            'file_path' => 'private/administrasi/denah_perpustakaan/denah.png',
            'tipe_file' => 'png',
            'ukuran_file' => 2048,
            'status' => 'aktif',
            'user_id' => $admin->id ?? null,
        ]);
    }
}
