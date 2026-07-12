<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyAnggotaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['siswa', 'guru', 'tendik'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $anggota = [
            ['Siti Nur Aisyah', 'siswa', 'P', 'Bandung', '2008-02-14', 'Jl. Merdeka No. 12', '081234560001'],
            ['Muhammad Rizky Pratama', 'siswa', 'L', 'Jakarta', '2007-09-21', 'Jl. Melati No. 4', '081234560002'],
            ['Nabila Putri Ramadhani', 'siswa', 'P', 'Garut', '2008-11-05', 'Jl. Kenanga No. 8', '081234560003'],
            ['Ahmad Fauzan Hakim', 'siswa', 'L', 'Tasikmalaya', '2007-05-17', 'Jl. Anggrek No. 10', '081234560004'],
            ['Dewi Lestari Maharani', 'siswa', 'P', 'Bogor', '2008-07-30', 'Jl. Cempaka No. 22', '081234560005'],
            ['Raka Aditya Nugraha', 'siswa', 'L', 'Cimahi', '2007-12-03', 'Jl. Flamboyan No. 6', '081234560006'],
            ['Maya Septiani', 'siswa', 'P', 'Subang', '2008-03-28', 'Jl. Mawar No. 19', '081234560007'],
            ['Fajar Maulana Yusuf', 'siswa', 'L', 'Cianjur', '2007-08-12', 'Jl. Dahlia No. 3', '081234560008'],
            ['Intan Permata Sari', 'siswa', 'P', 'Sukabumi', '2008-01-09', 'Jl. Teratai No. 15', '081234560009'],
            ['Bagas Saputra Wijaya', 'siswa', 'L', 'Bandung', '2007-10-26', 'Jl. Pahlawan No. 5', '081234560010'],
            ['Rina Kartika Dewi', 'guru', 'P', 'Bandung', '1986-04-11', 'Komplek Griya Asri Blok B2', '081234560011'],
            ['Agus Setiawan', 'guru', 'L', 'Sumedang', '1981-06-24', 'Jl. Pendidikan No. 7', '081234560012'],
            ['Sri Wahyuni', 'guru', 'P', 'Garut', '1989-01-18', 'Jl. Guru Bakti No. 2', '081234560013'],
            ['Hendra Gunawan', 'tendik', 'L', 'Bandung', '1979-09-02', 'Jl. Administrasi No. 9', '081234560014'],
            ['Lilis Suryani', 'tendik', 'P', 'Cirebon', '1984-12-20', 'Jl. Sekolah No. 11', '081234560015'],
        ];

        foreach ($anggota as $index => [$name, $type, $gender, $birthPlace, $birthDate, $address, $phone]) {
            $number = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $email = 'dummy.' . strtolower(str_replace(' ', '.', $name)) . '@smangunlib.test';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            if (method_exists($user, 'assignRole') && ! $user->hasRole($type)) {
                $user->assignRole($type);
            }

            Anggota::firstOrCreate(
                ['no_identitas' => 'DUMMY-' . strtoupper($type) . '-' . $number],
                [
                    'user_id' => $user->id,
                    'nomor_anggota' => 'AGT-' . now()->format('Y') . '-' . $number,
                    'tipe_anggota' => $type,
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
