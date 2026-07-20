<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Reservasi;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticatedPageSmokeTest extends TestCase
{
    use RefreshDatabase;

    private bool $installedFileExisted = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->installedFileExisted = file_exists(storage_path('app/installed'));

        if (! $this->installedFileExisted) {
            if (! is_dir(storage_path('app'))) {
                mkdir(storage_path('app'), 0777, true);
            }

            file_put_contents(storage_path('app/installed'), now()->toDateTimeString());
        }

        $this->seed(RolePermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        if (! $this->installedFileExisted && file_exists(storage_path('app/installed'))) {
            unlink(storage_path('app/installed'));
        }

        parent::tearDown();
    }

    public function test_super_admin_pages_render_successfully(): void
    {
        $user = User::create([
            'name' => 'Smoke Test Admin',
            'email' => 'smoke.admin@example.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('super_admin');

        $urls = [
            '/',
            '/dashboard',
            '/keanggotaan/anggota',
            '/koleksi/buku',
            '/sirkulasi',
            '/sirkulasi/peminjaman',
            '/sirkulasi/pengembalian',
            '/gls',
            '/gls/program',
            '/gls/program/create',
            '/gls/jurnal',
            '/manajemen-dokumen/dokumen',
            '/manajemen-dokumen/dokumen/create',
            '/laporan',
            '/system/info',
            '/system/license',
            '/system/backup',
            '/system/update',
            '/system/settings',
            '/system/contents',
            '/system/menus',
            '/system/media',
        ];

        foreach ($urls as $url) {
            $this->actingAs($user)
                ->get($url)
                ->assertOk();
        }
    }

    public function test_topbar_notifications_are_built_from_database_counts(): void
    {
        Carbon::setTestNow('2026-07-11 12:00:00');

        $user = User::create([
            'name' => 'Notification Test Admin',
            'email' => 'notification.admin@example.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('super_admin');

        $student = Anggota::create([
            'nomor_anggota' => 'AGT-TEST-0001',
            'tipe_anggota' => 'siswa',
            'no_identitas' => 'NIS-TEST-0001',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2008-01-01',
            'alamat' => 'Jl. Test',
            'status' => 'aktif',
            'masa_berlaku_sampai' => '2029-07-11',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Peminjaman::create([
            'nomor_transaksi' => 'TRX-TEST-0001',
            'anggota_id' => $student->id,
            'user_id' => $user->id,
            'tanggal_pinjam' => '2026-07-01',
            'due_date' => '2026-07-10',
            'status' => 'aktif',
        ]);

        $book = Buku::create([
            'judul' => 'Buku Test',
            'pengarang' => 'Penulis Test',
            'penerbit' => 'Penerbit Test',
            'tahun_terbit' => 2026,
            'bahasa' => 'Indonesia',
        ]);

        Reservasi::create([
            'anggota_id' => $student->id,
            'buku_id' => $book->id,
            'tanggal_reservasi' => Carbon::now(),
            'tanggal_kadaluarsa' => Carbon::now()->addDay(),
            'status' => 'tersedia',
        ]);

        $this->assertSame(1, Peminjaman::query()->whereIn('status', ['aktif', 'terlambat'])->whereDate('due_date', '<', Carbon::today())->count());
        $this->assertSame(1, Anggota::query()->where('tipe_anggota', 'siswa')->where('created_at', '>=', Carbon::now()->subDays(7))->count());
        $this->assertSame(1, Reservasi::query()->where('status', 'tersedia')->where('tanggal_kadaluarsa', '>=', Carbon::now())->count());

        $this->actingAs($user)
            ->get('/system/info')
            ->assertOk()
            ->assertSeeText('1 Buku melewati due date')
            ->assertSeeText('1 Registrasi siswa baru 7 hari terakhir')
            ->assertSeeText('1 Reservasi siap diambil');

        Carbon::setTestNow();
    }
}
