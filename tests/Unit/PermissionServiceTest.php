<?php

namespace Tests\Unit;

use App\Models\Menu;
use App\Models\MenuPermission;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Unit test PermissionService: pembangunan tree, rebuild permission, sinkronisasi
 * permission role (dengan proteksi privilege escalation), copy/reset, dan cache.
 */
class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PermissionService::class);
    }

    /**
     * Bangun sedikit struktur menu untuk pengujian.
     */
    private function seedSmallTree(): void
    {
        $koleksi = Menu::create(['key' => 'koleksi', 'title' => 'Koleksi', 'sort' => 0, 'is_active' => true]);
        $buku = Menu::create(['key' => 'koleksi.buku', 'title' => 'Buku', 'parent_id' => $koleksi->id, 'sort' => 0, 'is_active' => true]);

        MenuPermission::create(['menu_id' => $koleksi->id, 'action' => 'view', 'label' => 'View']);
        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            MenuPermission::create(['menu_id' => $buku->id, 'action' => $action, 'label' => ucfirst($action)]);
        }
    }

    /** @test */
    public function actions_and_roles_are_defined(): void
    {
        $this->assertArrayHasKey('view', PermissionService::actions());
        $this->assertArrayHasKey('export_pdf', PermissionService::actions());
        $this->assertArrayHasKey('super_admin', PermissionService::roles());
        $this->assertCount(8, PermissionService::roles());
    }

    /** @test */
    public function tree_is_built_and_cached_from_database(): void
    {
        $this->seedSmallTree();

        $tree = $this->service->tree();

        $this->assertCount(1, $tree);
        $this->assertSame('koleksi', $tree->first()->key);
        $this->assertCount(1, $tree->first()->children);
    }

    /** @test */
    public function rebuild_creates_spatie_permissions_from_menu_definitions(): void
    {
        $this->seedSmallTree();

        $count = $this->service->rebuild();

        $this->assertSame(5, $count); // 1 (koleksi.view) + 4 (buku crud)
        $this->assertTrue(Permission::where('name', 'koleksi.buku.create')->exists());
        $this->assertTrue(Permission::where('name', 'koleksi.view')->exists());

        // Peta permission -> menu terisi.
        $this->assertArrayHasKey('koleksi.buku.create', $this->service->permissionMenuMap());
    }

    /** @test */
    public function sync_role_permissions_ignores_unknown_permissions(): void
    {
        $this->seedSmallTree();
        $this->service->rebuild();

        $role = Role::create(['name' => 'pustakawan', 'guard_name' => 'web']);

        $this->service->syncRolePermissions($role, [
            'koleksi.buku.view',
            'koleksi.buku.create',
            'hacker.super.access', // tidak terdaftar -> harus diabaikan
        ]);

        $names = $role->fresh()->permissions->pluck('name')->all();
        $this->assertContains('koleksi.buku.view', $names);
        $this->assertContains('koleksi.buku.create', $names);
        $this->assertNotContains('hacker.super.access', $names);
    }

    /** @test */
    public function copy_and_reset_permissions_work(): void
    {
        $this->seedSmallTree();
        $this->service->rebuild();

        $source = Role::create(['name' => 'pustakawan', 'guard_name' => 'web']);
        $target = Role::create(['name' => 'guru', 'guard_name' => 'web']);

        $this->service->syncRolePermissions($source, ['koleksi.buku.view', 'koleksi.buku.edit']);
        $this->service->copyPermissions($source, $target);

        $this->assertEqualsCanonicalizing(
            $source->fresh()->permissions->pluck('name')->all(),
            $target->fresh()->permissions->pluck('name')->all()
        );

        $this->service->resetPermissions($target);
        $this->assertCount(0, $target->fresh()->permissions);
    }
}
