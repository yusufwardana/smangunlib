<?php

namespace Database\Seeders;

use App\Models\LandingContent;
use App\Models\LandingMenu;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SystemContentSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'school.nama_sekolah' => 'SMA Negeri',
            'school.alamat' => 'Jl. Pendidikan No. 1, Kota Sekolah',
            'school.telepon' => '(022) 1234 5678',
            'school.email' => 'perpustakaan@sma.sch.id',
            'school.kepala_sekolah' => 'Drs. Kepala Sekolah',
            'school.kepala_perpustakaan' => 'Kepala Perpustakaan',
            'landing.hero_title' => 'Selamat Datang di Perpustakaan SMA',
            'landing.hero_subtitle' => 'Menyediakan layanan informasi, koleksi buku, dan sumber belajar untuk mendukung kegiatan belajar mengajar.',
            'landing.hero_button_1' => 'Telusuri Koleksi',
            'landing.hero_link_1' => '#koleksi',
            'landing.hero_button_2' => 'Login',
            'landing.hero_link_2' => '/login',
            'landing.running_text' => 'Pengumuman: Pengembalian buku semester ini paling lambat Jumat pukul 14.00 WIB. Ikuti kegiatan 15 Menit Membaca setiap Selasa pagi.',
            'landing.profile_title' => 'Ruang belajar yang nyaman, tertib, dan mendukung budaya literasi.',
            'landing.profile_description' => 'Perpustakaan SMA hadir sebagai pusat informasi dan sumber belajar bagi siswa, guru, dan tenaga kependidikan.',
            'landing.profile_history' => 'Berkembang dari ruang baca sekolah menjadi layanan perpustakaan digital dan fisik yang terintegrasi.',
            'landing.profile_vision' => 'Menjadi pusat literasi sekolah yang inklusif, informatif, dan adaptif.',
            'landing.profile_mission' => 'Menyediakan koleksi bermutu, layanan ramah, dan program literasi berkelanjutan.',
            'landing.profile_goal' => 'Mendukung kegiatan belajar mengajar serta membangun kebiasaan membaca.',
            'library.lama_pinjam' => '7 hari',
            'library.maksimal_buku' => '3',
            'library.denda_per_hari' => '1000',
            'library.jam_operasional' => 'Senin - Jumat, 07.00 - 15.30 WIB',
            'contact.google_maps' => 'https://www.google.com/maps?q=Jakarta%20Indonesia&output=embed',
            'contact.alamat' => 'Jl. Pendidikan No. 1, Kota Sekolah',
            'contact.telepon' => '(022) 1234 5678',
            'contact.email' => 'perpustakaan@sma.sch.id',
            'contact.whatsapp' => '+62 812-3456-7890',
            'footer.copyright' => 'Hak Cipta '.date('Y').' Perpustakaan SMA Negeri. Didukung oleh SMANGUNLIB.',
            'footer.description' => 'Halaman informasi layanan, koleksi, literasi, dan pengumuman perpustakaan sekolah.',
            'seo.meta_title' => 'Perpustakaan SMA - SMANGUNLIB',
            'seo.meta_description' => 'Halaman depan Perpustakaan SMA untuk informasi koleksi buku, layanan, literasi, berita, dan kontak.',
            'seo.meta_keyword' => 'perpustakaan SMA, koleksi buku, literasi sekolah',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        foreach ([
            ['Beranda', '#beranda', 'bi-house', 1],
            ['Profil', '#profil', 'bi-building', 2],
            ['Koleksi', '#koleksi', 'bi-bookshelf', 3],
            ['Layanan', '#layanan', 'bi-journal-check', 4],
            ['Literasi', '#literasi', 'bi-lightbulb', 5],
            ['Berita', '#berita', 'bi-newspaper', 6],
            ['Kontak', '#kontak', 'bi-telephone', 7],
        ] as [$name, $url, $icon, $order]) {
            LandingMenu::firstOrCreate(['name' => $name], ['url' => $url, 'icon' => $icon, 'sort_order' => $order, 'is_active' => true]);
        }

        foreach ([
            ['stat', 'Total Koleksi Buku', 'bi-bookshelf', '12.450'],
            ['stat', 'Buku Digital', 'bi-tablet', '320'],
            ['stat', 'Anggota Perpustakaan', 'bi-people', '1.280'],
            ['stat', 'Buku Dipinjam', 'bi-arrow-left-right', '186'],
            ['stat', 'Pengunjung Bulan Ini', 'bi-person-check', '2.430'],
            ['book_category', 'Buku Pelajaran', 'bi-calculator', 'Koleksi buku pendukung pembelajaran.'],
            ['book_category', 'Referensi', 'bi-bookmark-star', 'Sumber rujukan untuk tugas dan penelitian.'],
            ['book_category', 'Fiksi', 'bi-feather', 'Cerita dan sastra pilihan.'],
            ['book_category', 'Nonfiksi', 'bi-newspaper', 'Pengetahuan populer dan wawasan umum.'],
            ['book_category', 'Majalah', 'bi-file-richtext', 'Majalah sekolah dan pendidikan.'],
            ['book_category', 'Kamus', 'bi-translate', 'Kamus bahasa dan istilah.'],
            ['book_category', 'Ensiklopedia', 'bi-globe2', 'Ensiklopedia ilmu pengetahuan.'],
            ['book_category', 'Karya Ilmiah', 'bi-clipboard-data', 'Karya tulis dan riset siswa.'],
            ['book_highlight', 'Matematika Lanjut SMA', 'bi-book', 'Tim Edukasi|Buku Pelajaran'],
            ['book_highlight', 'Fisika Kontekstual', 'bi-book', 'Dr. R. Santosa|Referensi'],
            ['book_highlight', 'Jejak Literasi Nusantara', 'bi-book', 'Dewi Lestari|Nonfiksi'],
            ['book_highlight', 'Cerita dari Ruang Kelas', 'bi-book', 'A. Pratama|Fiksi'],
            ['service', 'Layanan Peminjaman', 'bi-journal-arrow-up', 'Peminjaman buku dengan proses sederhana dan tercatat.'],
            ['service', 'Layanan Pengembalian', 'bi-journal-check', 'Pengembalian buku dan pengecekan keterlambatan.'],
            ['service', 'Layanan Referensi', 'bi-search-heart', 'Bantuan menemukan sumber informasi yang relevan.'],
            ['service', 'Layanan Membaca', 'bi-cup-hot', 'Ruang baca nyaman untuk siswa dan guru.'],
            ['service', 'Layanan Digital', 'bi-cloud-check', 'Akses informasi dan koleksi digital sekolah.'],
            ['service', 'Bimbingan Literasi', 'bi-lightbulb', 'Pendampingan kegiatan literasi dan referensi.'],
            ['literacy_program', 'Gerakan Literasi Sekolah', 'bi-book', 'Program pembiasaan membaca dan menulis di sekolah.'],
            ['literacy_program', '15 Menit Membaca', 'bi-clock', 'Kegiatan membaca pagi bersama wali kelas.'],
            ['literacy_program', 'Bedah Buku', 'bi-chat-square-text', 'Diskusi buku pilihan bersama siswa dan guru.'],
            ['literacy_program', 'Lomba Literasi', 'bi-trophy', 'Kompetisi resensi, puisi, dan karya tulis.'],
            ['literacy_program', 'Pojok Baca', 'bi-bookmark-heart', 'Area baca ringan di lingkungan sekolah.'],
        ] as $index => [$type, $title, $icon, $description]) {
            LandingContent::firstOrCreate(['type' => $type, 'title' => $title], [
                'icon' => $icon,
                'description' => $description,
                'status' => 'active',
                'sort_order' => $index + 1,
            ]);
        }

        foreach ([
            ['calendar_event', 'Bedah Buku', '09 Jul', 'Diskusi buku pilihan bersama siswa.'],
            ['calendar_event', 'Lomba Resensi', '16 Jul', 'Kompetisi resensi buku tingkat sekolah.'],
            ['calendar_event', 'Kelas Referensi', '23 Jul', 'Pelatihan penelusuran sumber informasi.'],
            ['download', 'Download Tata Tertib Perpustakaan', 'bi-filetype-pdf', '#'],
            ['download', 'Download Panduan Anggota', 'bi-file-earmark-arrow-down', '#'],
        ] as $index => [$type, $title, $icon, $description]) {
            LandingContent::firstOrCreate(['type' => $type, 'title' => $title], [
                'icon' => $icon,
                'description' => $description,
                'status' => 'active',
                'sort_order' => $index + 1,
            ]);
        }

        foreach ([
            ['Orientasi Anggota Baru Perpustakaan', 'Pengenalan tata tertib dan layanan perpustakaan untuk siswa baru.'],
            ['Jadwal 15 Menit Membaca Pekan Ini', 'Kegiatan membaca pagi dilaksanakan di kelas bersama wali kelas.'],
            ['Pengembalian Buku Semester Genap', 'Siswa diminta mengembalikan buku pinjaman sebelum akhir pekan.'],
            ['Pameran Buku Fiksi Remaja', 'Koleksi fiksi pilihan tersedia di area display perpustakaan.'],
            ['Pelatihan Penelusuran Referensi', 'Guru dan siswa mengikuti sesi penggunaan katalog online.'],
            ['Donasi Buku Alumni', 'Perpustakaan menerima tambahan koleksi dari alumni sekolah.'],
        ] as $index => [$title, $description]) {
            LandingContent::firstOrCreate(['type' => 'news', 'title' => $title], [
                'description' => $description,
                'body' => $description,
                'author' => 'Perpustakaan',
                'published_at' => now()->subDays($index),
                'status' => 'published',
                'sort_order' => $index + 1,
            ]);
        }

        foreach ([
            ['Bagaimana cara menjadi anggota perpustakaan?', 'Siswa dan guru otomatis dapat menggunakan layanan perpustakaan setelah data anggota terdaftar.'],
            ['Berapa lama masa pinjam buku?', 'Masa pinjam mengikuti pengaturan perpustakaan, umumnya 7 hari.'],
            ['Apakah tersedia buku digital?', 'Ya, koleksi digital dapat diakses sesuai kebijakan sekolah.'],
            ['Bagaimana jika terlambat mengembalikan buku?', 'Sistem akan menghitung keterlambatan sesuai aturan denda yang berlaku.'],
        ] as $index => [$title, $body]) {
            LandingContent::firstOrCreate(['type' => 'faq', 'title' => $title], [
                'body' => $body,
                'status' => 'active',
                'sort_order' => $index + 1,
            ]);
        }
    }
}
