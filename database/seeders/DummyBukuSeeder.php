<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Eksemplar;
use App\Models\Kategori;
use App\Models\RakLokasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DummyBukuSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = collect([
            ['kode_ddc' => '000', 'nama_kategori' => 'Karya Umum'],
            ['kode_ddc' => '100', 'nama_kategori' => 'Filsafat dan Psikologi'],
            ['kode_ddc' => '200', 'nama_kategori' => 'Agama'],
            ['kode_ddc' => '300', 'nama_kategori' => 'Ilmu Sosial'],
            ['kode_ddc' => '370', 'nama_kategori' => 'Pendidikan'],
            ['kode_ddc' => '400', 'nama_kategori' => 'Bahasa'],
            ['kode_ddc' => '500', 'nama_kategori' => 'Sains'],
            ['kode_ddc' => '600', 'nama_kategori' => 'Teknologi'],
            ['kode_ddc' => '700', 'nama_kategori' => 'Seni dan Olahraga'],
            ['kode_ddc' => '800', 'nama_kategori' => 'Sastra'],
            ['kode_ddc' => '900', 'nama_kategori' => 'Sejarah dan Geografi'],
        ])->mapWithKeys(fn (array $item) => [
            $item['kode_ddc'] => Kategori::updateOrCreate(
                ['kode_ddc' => $item['kode_ddc']],
                ['nama_kategori' => $item['nama_kategori']]
            ),
        ]);

        $rak = collect([
            ['kode_rak' => 'RAK-REF', 'nama_lokasi' => 'Rak Referensi', 'deskripsi' => 'Kamus, ensiklopedia, dan buku rujukan.'],
            ['kode_rak' => 'RAK-FIK', 'nama_lokasi' => 'Rak Fiksi', 'deskripsi' => 'Novel, cerpen, puisi, dan sastra populer.'],
            ['kode_rak' => 'RAK-SAI', 'nama_lokasi' => 'Rak Sains', 'deskripsi' => 'Matematika, IPA, dan pengetahuan alam.'],
            ['kode_rak' => 'RAK-SOS', 'nama_lokasi' => 'Rak Sosial', 'deskripsi' => 'IPS, kewarganegaraan, ekonomi, dan sosial.'],
            ['kode_rak' => 'RAK-TEK', 'nama_lokasi' => 'Rak Teknologi', 'deskripsi' => 'Komputer, keterampilan, dan teknologi terapan.'],
            ['kode_rak' => 'RAK-AGM', 'nama_lokasi' => 'Rak Agama', 'deskripsi' => 'Buku agama dan akhlak.'],
        ])->mapWithKeys(fn (array $item) => [
            $item['kode_rak'] => RakLokasi::updateOrCreate(
                ['kode_rak' => $item['kode_rak']],
                ['nama_lokasi' => $item['nama_lokasi'], 'deskripsi' => $item['deskripsi']]
            ),
        ]);

        $books = [
            ['isbn' => '9786020332956', 'judul' => 'Laskar Pelangi', 'pengarang' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => 2005, 'edisi' => 'Cetakan 1', 'halaman' => 529, 'bahasa' => 'Indonesia', 'ddc' => ['800'], 'rak' => 'RAK-FIK', 'copies' => 5],
            ['isbn' => '9789793062792', 'judul' => 'Negeri 5 Menara', 'pengarang' => 'Ahmad Fuadi', 'penerbit' => 'Gramedia Pustaka Utama', 'tahun_terbit' => 2009, 'edisi' => 'Cetakan 1', 'halaman' => 423, 'bahasa' => 'Indonesia', 'ddc' => ['800', '370'], 'rak' => 'RAK-FIK', 'copies' => 4],
            ['isbn' => '9786022910367', 'judul' => 'Bumi Manusia', 'pengarang' => 'Pramoedya Ananta Toer', 'penerbit' => 'Lentera Dipantara', 'tahun_terbit' => 1980, 'edisi' => 'Edisi Baru', 'halaman' => 551, 'bahasa' => 'Indonesia', 'ddc' => ['800', '900'], 'rak' => 'RAK-FIK', 'copies' => 3],
            ['isbn' => '9789792281415', 'judul' => 'Ayat-Ayat Cinta', 'pengarang' => 'Habiburrahman El Shirazy', 'penerbit' => 'Republika', 'tahun_terbit' => 2004, 'edisi' => 'Cetakan 1', 'halaman' => 418, 'bahasa' => 'Indonesia', 'ddc' => ['800', '200'], 'rak' => 'RAK-FIK', 'copies' => 4],
            ['isbn' => '9786024246945', 'judul' => 'Hujan', 'pengarang' => 'Tere Liye', 'penerbit' => 'Gramedia Pustaka Utama', 'tahun_terbit' => 2016, 'edisi' => 'Cetakan 1', 'halaman' => 320, 'bahasa' => 'Indonesia', 'ddc' => ['800'], 'rak' => 'RAK-FIK', 'copies' => 5],
            ['isbn' => '9786020331607', 'judul' => 'Pulang', 'pengarang' => 'Tere Liye', 'penerbit' => 'Republika', 'tahun_terbit' => 2015, 'edisi' => 'Cetakan 1', 'halaman' => 400, 'bahasa' => 'Indonesia', 'ddc' => ['800'], 'rak' => 'RAK-FIK', 'copies' => 4],
            ['isbn' => '9789794338179', 'judul' => 'Sang Pemimpi', 'pengarang' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => 2006, 'edisi' => 'Cetakan 1', 'halaman' => 292, 'bahasa' => 'Indonesia', 'ddc' => ['800'], 'rak' => 'RAK-FIK', 'copies' => 4],
            ['isbn' => '9786020315416', 'judul' => 'Dilan: Dia adalah Dilanku Tahun 1990', 'pengarang' => 'Pidi Baiq', 'penerbit' => 'Pastel Books', 'tahun_terbit' => 2014, 'edisi' => 'Cetakan 1', 'halaman' => 332, 'bahasa' => 'Indonesia', 'ddc' => ['800'], 'rak' => 'RAK-FIK', 'copies' => 5],
            ['isbn' => '9786020620930', 'judul' => 'Cantik Itu Luka', 'pengarang' => 'Eka Kurniawan', 'penerbit' => 'Gramedia Pustaka Utama', 'tahun_terbit' => 2002, 'edisi' => 'Edisi Revisi', 'halaman' => 505, 'bahasa' => 'Indonesia', 'ddc' => ['800'], 'rak' => 'RAK-FIK', 'copies' => 2],
            ['isbn' => '9786020633176', 'judul' => 'Laut Bercerita', 'pengarang' => 'Leila S. Chudori', 'penerbit' => 'Kepustakaan Populer Gramedia', 'tahun_terbit' => 2017, 'edisi' => 'Cetakan 1', 'halaman' => 379, 'bahasa' => 'Indonesia', 'ddc' => ['800', '900'], 'rak' => 'RAK-FIK', 'copies' => 3],
            ['isbn' => '9789790257061', 'judul' => 'Kamus Besar Bahasa Indonesia', 'pengarang' => 'Tim Penyusun', 'penerbit' => 'Balai Pustaka', 'tahun_terbit' => 2018, 'edisi' => 'Edisi Kelima', 'halaman' => 1964, 'bahasa' => 'Indonesia', 'ddc' => ['400', '000'], 'rak' => 'RAK-REF', 'copies' => 2],
            ['isbn' => '9786020631141', 'judul' => 'Ensiklopedia Sains untuk Pelajar', 'pengarang' => 'Tim Redaksi', 'penerbit' => 'Gramedia Pustaka Utama', 'tahun_terbit' => 2020, 'edisi' => 'Cetakan 1', 'halaman' => 256, 'bahasa' => 'Indonesia', 'ddc' => ['500', '000'], 'rak' => 'RAK-REF', 'copies' => 3],
            ['isbn' => '9786024271404', 'judul' => 'Matematika untuk SMA/MA Kelas X', 'pengarang' => 'Kemendikbud', 'penerbit' => 'Pusat Kurikulum dan Perbukuan', 'tahun_terbit' => 2021, 'edisi' => 'Kurikulum Merdeka', 'halaman' => 312, 'bahasa' => 'Indonesia', 'ddc' => ['500', '370'], 'rak' => 'RAK-SAI', 'copies' => 8],
            ['isbn' => '9786024271411', 'judul' => 'Biologi untuk SMA/MA Kelas X', 'pengarang' => 'Kemendikbud', 'penerbit' => 'Pusat Kurikulum dan Perbukuan', 'tahun_terbit' => 2021, 'edisi' => 'Kurikulum Merdeka', 'halaman' => 280, 'bahasa' => 'Indonesia', 'ddc' => ['500', '370'], 'rak' => 'RAK-SAI', 'copies' => 8],
            ['isbn' => '9786024271428', 'judul' => 'Fisika untuk SMA/MA Kelas X', 'pengarang' => 'Kemendikbud', 'penerbit' => 'Pusat Kurikulum dan Perbukuan', 'tahun_terbit' => 2021, 'edisi' => 'Kurikulum Merdeka', 'halaman' => 296, 'bahasa' => 'Indonesia', 'ddc' => ['500', '370'], 'rak' => 'RAK-SAI', 'copies' => 8],
            ['isbn' => '9786024271435', 'judul' => 'Kimia untuk SMA/MA Kelas X', 'pengarang' => 'Kemendikbud', 'penerbit' => 'Pusat Kurikulum dan Perbukuan', 'tahun_terbit' => 2021, 'edisi' => 'Kurikulum Merdeka', 'halaman' => 288, 'bahasa' => 'Indonesia', 'ddc' => ['500', '370'], 'rak' => 'RAK-SAI', 'copies' => 8],
            ['isbn' => '9786024271442', 'judul' => 'Sejarah Indonesia untuk SMA/MA', 'pengarang' => 'Kemendikbud', 'penerbit' => 'Pusat Kurikulum dan Perbukuan', 'tahun_terbit' => 2020, 'edisi' => 'Kurikulum 2013', 'halaman' => 260, 'bahasa' => 'Indonesia', 'ddc' => ['900', '370'], 'rak' => 'RAK-SOS', 'copies' => 6],
            ['isbn' => '9786024271459', 'judul' => 'Ekonomi untuk SMA/MA Kelas XI', 'pengarang' => 'Kemendikbud', 'penerbit' => 'Pusat Kurikulum dan Perbukuan', 'tahun_terbit' => 2020, 'edisi' => 'Kurikulum 2013', 'halaman' => 274, 'bahasa' => 'Indonesia', 'ddc' => ['300', '370'], 'rak' => 'RAK-SOS', 'copies' => 6],
            ['isbn' => '9786024271466', 'judul' => 'Pendidikan Pancasila dan Kewarganegaraan', 'pengarang' => 'Kemendikbud', 'penerbit' => 'Pusat Kurikulum dan Perbukuan', 'tahun_terbit' => 2022, 'edisi' => 'Kurikulum Merdeka', 'halaman' => 248, 'bahasa' => 'Indonesia', 'ddc' => ['300', '370'], 'rak' => 'RAK-SOS', 'copies' => 7],
            ['isbn' => '9786026232441', 'judul' => 'Pengantar Teknologi Informasi', 'pengarang' => 'Budi Raharjo', 'penerbit' => 'Informatika', 'tahun_terbit' => 2019, 'edisi' => 'Edisi 2', 'halaman' => 220, 'bahasa' => 'Indonesia', 'ddc' => ['600'], 'rak' => 'RAK-TEK', 'copies' => 4],
            ['isbn' => '9786021514573', 'judul' => 'Dasar-Dasar Pemrograman', 'pengarang' => 'Abdul Kadir', 'penerbit' => 'Andi Offset', 'tahun_terbit' => 2018, 'edisi' => 'Cetakan 1', 'halaman' => 340, 'bahasa' => 'Indonesia', 'ddc' => ['600'], 'rak' => 'RAK-TEK', 'copies' => 4],
            ['isbn' => '9789792908947', 'judul' => 'Mahir Microsoft Office', 'pengarang' => 'Wahana Komputer', 'penerbit' => 'Andi Offset', 'tahun_terbit' => 2020, 'edisi' => 'Cetakan 1', 'halaman' => 216, 'bahasa' => 'Indonesia', 'ddc' => ['600'], 'rak' => 'RAK-TEK', 'copies' => 5],
            ['isbn' => '9786020523668', 'judul' => 'Pendidikan Agama Islam dan Budi Pekerti', 'pengarang' => 'Tim Guru', 'penerbit' => 'Erlangga', 'tahun_terbit' => 2021, 'edisi' => 'Kurikulum Merdeka', 'halaman' => 232, 'bahasa' => 'Indonesia', 'ddc' => ['200', '370'], 'rak' => 'RAK-AGM', 'copies' => 6],
            ['isbn' => '9789790336292', 'judul' => 'Akhlak Mulia untuk Remaja', 'pengarang' => 'M. Quraish Shihab', 'penerbit' => 'Mizan', 'tahun_terbit' => 2017, 'edisi' => 'Cetakan 1', 'halaman' => 180, 'bahasa' => 'Indonesia', 'ddc' => ['200', '100'], 'rak' => 'RAK-AGM', 'copies' => 3],
            ['isbn' => '9786020485928', 'judul' => 'Seni Budaya untuk SMA/MA', 'pengarang' => 'Tim Guru Seni', 'penerbit' => 'Erlangga', 'tahun_terbit' => 2021, 'edisi' => 'Kurikulum 2013', 'halaman' => 240, 'bahasa' => 'Indonesia', 'ddc' => ['700', '370'], 'rak' => 'RAK-SOS', 'copies' => 5],
        ];

        foreach ($books as $index => $item) {
            $buku = Buku::updateOrCreate(
                ['isbn' => $item['isbn']],
                [
                    'judul' => $item['judul'],
                    'pengarang' => $item['pengarang'],
                    'penerbit' => $item['penerbit'],
                    'tahun_terbit' => $item['tahun_terbit'],
                    'edisi' => $item['edisi'],
                    'halaman' => $item['halaman'],
                    'bahasa' => $item['bahasa'],
                    'deskripsi' => 'Data dummy buku perpustakaan untuk simulasi katalog, OPAC, dan sirkulasi.',
                    'rak_lokasi_id' => $rak[$item['rak']]->id,
                    'is_digital' => false,
                ]
            );

            $buku->kategori()->sync(collect($item['ddc'])->map(fn (string $ddc) => $kategori[$ddc]->id)->all());

            for ($copy = 1; $copy <= $item['copies']; $copy++) {
                Eksemplar::updateOrCreate(
                    ['nomor_barcode' => sprintf('BK-%04d-%02d', $index + 1, $copy)],
                    [
                        'buku_id' => $buku->id,
                        'tanggal_pengadaan' => Carbon::create(2026, 7, 1)->addDays($index),
                        'asal_pengadaan' => $copy % 3 === 0 ? 'Bantuan BOS' : 'Pembelian Sekolah',
                        'harga' => 45000 + (($index % 8) * 7500),
                        'kondisi' => $copy % 11 === 0 ? 'rusak_ringan' : 'baik',
                        'status_sirkulasi' => 'tersedia',
                    ]
                );
            }
        }
    }
}
