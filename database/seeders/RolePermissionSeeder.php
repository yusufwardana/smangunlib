<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Permissions
        $permissions = [
            'view dashboard',
            'manage settings',
            'manage users',
            'manage koleksi',
            'view koleksi',
            'manage sirkulasi',
            'view sirkulasi',
            'manage anggota',
            'view laporan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Buat Roles & Assign Permissions
        
        // Role: Super Admin (punya akses ke semuanya secara default melalui Gate di AppServiceProvider)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        
        // Role: Kepala Sekolah
        $roleKepalaSekolah = Role::firstOrCreate(['name' => 'kepala_sekolah']);
        $roleKepalaSekolah->givePermissionTo(['view dashboard', 'view laporan']);
        
        // Role: Kepala Perpustakaan
        $roleKaPerpus = Role::firstOrCreate(['name' => 'kepala_perpustakaan']);
        $roleKaPerpus->givePermissionTo([
            'view dashboard', 'manage koleksi', 'view koleksi', 
            'manage sirkulasi', 'view sirkulasi', 'manage anggota', 
            'view laporan'
        ]);
        
        // Role: Pustakawan
        $rolePustakawan = Role::firstOrCreate(['name' => 'pustakawan']);
        $rolePustakawan->givePermissionTo([
            'view dashboard', 'manage koleksi', 'view koleksi', 
            'manage sirkulasi', 'view sirkulasi', 'manage anggota'
        ]);
        
        // Role: Guru
        $roleGuru = Role::firstOrCreate(['name' => 'guru']);
        $roleGuru->givePermissionTo(['view dashboard', 'view koleksi']);
        
        // Role: Siswa
        $roleSiswa = Role::firstOrCreate(['name' => 'siswa']);
        $roleSiswa->givePermissionTo(['view dashboard', 'view koleksi']);

        // 3. Buat User Super Admin Awal
        $superAdmin = User::firstOrCreate([
            'email' => 'admin@smangunlib.sch.id'
        ], [
            'name' => 'Super Administrator',
            'password' => Hash::make('password123'),
        ]);

        $superAdmin->assignRole('super_admin');
    }
}
