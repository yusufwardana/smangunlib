<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use App\Services\PermissionService;
use Database\Seeders\MenuPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Feature test modul "Pengaturan Hak Akses Menu" (RBAC).
 *
 * Menguji:
 *  - Sidebar mengikuti permission (menu tampil/tidak).
 *  - Route ditolak (403) jika role tidak memiliki akses & lolos untuk super admin.
 *  - Tombol CRUD mengikuti permission.
 *  - Menu mengikuti permission (canDo/canViewMenu).
 *  - Aksi manajemen: update, copy, reset, rebuild, clear cache + audit log.
 */
class MenuPermissionTest extends TestCase
{
    use RefreshDatabase;

    private bool $installedFileExisted = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Bypass installer middleware.
        $this->installedFileExisted = file_exists(storage_path('app/installed'));
        if (! $this->installedFileExisted) {
            if (! is_dir(storage_path('app'))) {
                mkdir(storage_path('app'), 0777, true);
            }
            file_put_contents(storage_path('app/installed'), now()->toDateTimeString());
        }

        $this->seed(MenuPermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        if (! $this->installedFileExisted && file_exists(storage_path('app/installed'))) {
            unlink(storage_path('app/installed'));
        }
        parent::tearDown();
    }

    private function userWithRole(string $role): User
    {
        $user = User::create([
            'name'     => ucfirst($role),
            'email'    => $role.'@example.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole($role);

        return $user;
    }

    /** @test */
    public function super_admin_can_open_menu_permission_page(): void
    {
        $this->actingAs($this->userWithRole('super_admin'))
            ->get(route('system.permissions.index'))
            ->assertOk()
            ->assertSee('Pengaturan Hak Akses Menu');
    }

    /** @test */
    public function non_super_admin_is_denied_from_menu_permission_page(): void
    {
        $this->actingAs($this->userWithRole('guru'))
            ->get(route('system.permissions.index'))
            ->assertForbidden();
    }

    /** @test */
    public function sidebar_follows_permission(): void
    {
        // Pustakawan memiliki koleksi & sirkulasi namun tidak punya laporan penuh? cek koleksi ada, backup tidak.
        $service = app(PermissionService::class);

        $pustakawan = $this->userWithRole('pustakawan');
        $sidebarKeys = collect($service->sidebarFor($pustakawan))->pluck('key');

        $this->assertTrue($sidebarKeys->contains('koleksi'), 'Pustakawan harus melihat menu Koleksi');
        $this->assertTrue($sidebarKeys->contains('dashboard'), 'Pustakawan harus melihat Dashboard');

        // Guru tidak boleh melihat menu sistem.
        $guru = $this->userWithRole('guru');
        $guruKeys = collect($service->sidebarFor($guru))->pluck('key');
        $this->assertFalse($guruKeys->contains('sistem'), 'Guru tidak boleh melihat System Management');
    }

    /** @test */
    public function menu_and_button_follow_permission(): void
    {
        $service = app(PermissionService::class);

        $pustakawan = $this->userWithRole('pustakawan');
        $this->assertTrue($service->canDo($pustakawan, 'koleksi.buku', 'view'));
        $this->assertTrue($service->canDo($pustakawan, 'koleksi.buku', 'create'));

        $guru = $this->userWithRole('guru');
        $this->assertTrue($service->canDo($guru, 'koleksi.buku', 'view'));
        $this->assertFalse($service->canDo($guru, 'koleksi.buku', 'create'), 'Guru tidak boleh membuat buku');
    }

    /** @test */
    public function route_is_denied_when_role_lacks_permission(): void
    {
        // Buat route dummy terlindungi middleware menu untuk aksi yang tidak dimiliki guru.
        \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'menu:koleksi.buku.create'])
            ->get('/__test/book-create', fn () => 'ok');

        $this->actingAs($this->userWithRole('guru'))
            ->get('/__test/book-create')
            ->assertForbidden();

        $this->actingAs($this->userWithRole('pustakawan'))
            ->get('/__test/book-create')
            ->assertOk();
    }

    /** @test */
    public function super_admin_bypasses_permission_middleware(): void
    {
        \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'menu:koleksi.buku.delete'])
            ->get('/__test/book-delete', fn () => 'ok');

        $this->actingAs($this->userWithRole('super_admin'))
            ->get('/__test/book-delete')
            ->assertOk();
    }

    /** @test */
    public function super_admin_can_update_role_permissions_and_audit_is_recorded(): void
    {
        $admin = $this->userWithRole('super_admin');
        $guru = Role::where('name', 'guru')->first();

        $this->actingAs($admin)
            ->put(route('system.permissions.update', $guru), [
                'permissions' => ['dashboard.view', 'koleksi.buku.view', 'koleksi.buku.create'],
            ])
            ->assertRedirect();

        $guru->refresh();
        $this->assertTrue($guru->hasPermissionTo('koleksi.buku.create'));

        $this->assertDatabaseHas('audit_logs', ['action' => 'update_menu_permission']);
    }

    /** @test */
    public function super_admin_can_copy_and_reset_permissions(): void
    {
        $admin = $this->userWithRole('super_admin');
        $pustakawan = Role::where('name', 'pustakawan')->first();
        $guru = Role::where('name', 'guru')->first();

        // Copy dari pustakawan ke guru.
        $this->actingAs($admin)
            ->post(route('system.permissions.copy', $guru), ['source_role_id' => $pustakawan->id])
            ->assertRedirect();

        $guru->refresh();
        $this->assertEqualsCanonicalizing(
            $pustakawan->permissions->pluck('name')->all(),
            $guru->permissions->pluck('name')->all()
        );

        // Reset guru.
        $this->actingAs($admin)
            ->post(route('system.permissions.reset', $guru))
            ->assertRedirect();

        $this->assertCount(0, $guru->fresh()->permissions);
        $this->assertDatabaseHas('audit_logs', ['action' => 'reset_menu_permission']);
    }

    /** @test */
    public function super_admin_can_rebuild_and_clear_permission_cache(): void
    {
        $admin = $this->userWithRole('super_admin');

        $this->actingAs($admin)->post(route('system.permissions.rebuild'))->assertRedirect();
        $this->actingAs($admin)->post(route('system.permissions.clear-cache'))->assertRedirect();

        $this->assertDatabaseHas('audit_logs', ['action' => 'rebuild_permission']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'clear_permission_cache']);
    }

    /** @test */
    public function widget_visibility_follows_permission(): void
    {
        // Widget dashboard mengikuti permission dashboard.view.
        $service = app(PermissionService::class);

        $guest = $this->userWithRole('guest');
        $this->assertFalse($service->canDo($guest, 'dashboard', 'view'), 'Guest tidak boleh melihat widget dashboard');

        $guru = $this->userWithRole('guru');
        $this->assertTrue($service->canDo($guru, 'dashboard', 'view'));
    }
}
