<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Super Administrator', 'superadmin@smangunlib.test', 'super_admin', null, 'L', 'Bandung', '1980-01-12', 'Jl. Sekolah No. 1', '081200000001'],
            ['Drs. Budi Santoso', 'kepala.sekolah@smangunlib.test', 'kepala_sekolah', null, 'L', 'Bandung', '1972-04-08', 'Komplek Guru Indah A1', '081200000002'],
            ['Dra. Ratna Wulandari', 'kepala.perpustakaan@smangunlib.test', 'kepala_perpustakaan', 'guru', 'P', 'Garut', '1978-10-21', 'Jl. Literasi No. 7', '081200000003'],
            ['Mira Pustakawati', 'pustakawan@smangunlib.test', 'pustakawan', 'tendik', 'P', 'Cimahi', '1987-03-15', 'Jl. Buku Raya No. 3', '081200000004'],
            ['Andi Prasetyo', 'guru@smangunlib.test', 'guru', 'guru', 'L', 'Sumedang', '1985-07-19', 'Jl. Pendidikan No. 12', '081200000005'],
            ['Nadia Amalia', 'guru.bahasa@smangunlib.test', 'guru', 'guru', 'P', 'Tasikmalaya', '1990-11-02', 'Jl. Pena No. 9', '081200000006'],
            ['Rafi Maulana', 'siswa@smangunlib.test', 'siswa', 'siswa', 'L', 'Bandung', '2008-05-17', 'Jl. Melati No. 18', '081200000007'],
            ['Aulia Fitriani', 'siswa.ips@smangunlib.test', 'siswa', 'siswa', 'P', 'Bogor', '2008-09-24', 'Jl. Kenanga No. 11', '081200000008'],
            ['Dimas Saputra', 'siswa.ipa@smangunlib.test', 'siswa', 'siswa', 'L', 'Cianjur', '2007-12-04', 'Jl. Dahlia No. 5', '081200000009'],
            ['Tina Marlina', 'tendik@smangunlib.test', 'tendik', 'tendik', 'P', 'Cirebon', '1983-06-30', 'Jl. Administrasi No. 6', '081200000010'],
        ];

        foreach (['super_admin', 'kepala_sekolah', 'kepala_perpustakaan', 'pustakawan', 'guru', 'siswa', 'tendik'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        foreach ($users as $index => [$name, $email, $role, $memberType, $gender, $birthPlace, $birthDate, $address, $phone]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$role]);

            if (! $memberType) {
                continue;
            }

            $number = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            Anggota::updateOrCreate(
                ['no_identitas' => 'USR-' . strtoupper($memberType) . '-' . $number],
                [
                    'user_id' => $user->id,
                    'nomor_anggota' => 'USR-' . now()->format('Y') . '-' . $number,
                    'tipe_anggota' => $memberType,
                    'jenis_kelamin' => $gender,
                    'tempat_lahir' => $birthPlace,
                    'tanggal_lahir' => $birthDate,
                    'alamat' => $address,
                    'no_telepon' => $phone,
                    'status' => 'aktif',
                    'masa_berlaku_sampai' => now()->addYears(3)->toDateString(),
                ]
            );
        }
    }
}
