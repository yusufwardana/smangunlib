<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\ActivityLogger;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

/**
 * MenuPermissionController
 *
 * Modul "Pengaturan Hak Akses Menu" (RBAC). Hanya Super Admin yang berhak.
 * Menyediakan:
 *  - Matriks pohon menu x aksi per role (view/create/edit/…).
 *  - Simpan perubahan permission per role (dengan audit log lengkap).
 *  - Copy permission dari role lain, reset permission.
 *  - Rebuild permission dari definisi menu & clear cache permission.
 *  - Tampilan Audit Log perubahan permission.
 */
class MenuPermissionController extends Controller
{
    public function __construct(private readonly PermissionService $permissions)
    {
    }

    /**
     * Pastikan hanya Super Admin yang boleh mengelola hak akses menu.
     */
    private function authorizeManage(): void
    {
        abort_unless(
            Gate::allows('manage', \App\Models\Menu::class),
            403,
            'Anda tidak memiliki hak akses.'
        );
    }

    /**
     * Tampilkan matriks pengaturan hak akses menu untuk sebuah role.
     */
    public function index(Request $request)
    {
        $this->authorizeManage();

        $roles = Role::orderBy('name')->get();
        $roleNames = PermissionService::roles();

        // Role terpilih (default: role pertama selain super_admin bila ada).
        $selectedName = $request->query('role');
        $selected = $selectedName
            ? Role::where('name', $selectedName)->first()
            : $roles->firstWhere('name', '!=', 'super_admin') ?? $roles->first();

        abort_if($selected === null, 404, 'Role tidak ditemukan.');

        $tree = $this->permissions->tree();
        $actions = PermissionService::actions();
        $granted = $this->permissions->permissionsOfRole($selected);

        return view('system.permissions.index', [
            'roles'        => $roles,
            'roleNames'    => $roleNames,
            'selected'     => $selected,
            'tree'         => $tree,
            'actions'      => $actions,
            'granted'      => $granted,
            'isSuperAdmin' => $selected->name === 'super_admin',
        ]);
    }

    /**
     * Simpan perubahan permission untuk sebuah role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['required', 'string', 'max:120'],
        ]);

        // Super Admin tidak boleh dibatasi (selalu punya seluruh akses).
        if ($role->name === 'super_admin') {
            return back()->with('info', 'Super Admin selalu memiliki seluruh hak akses dan tidak dapat diubah.');
        }

        $before = $this->permissions->permissionsOfRole($role);

        $this->permissions->syncRolePermissions($role, $validated['permissions'] ?? []);

        $after = $this->permissions->permissionsOfRole($role->fresh());

        // Audit log: siapa, kapan, role, permission lama, permission baru.
        ActivityLogger::log('update_menu_permission', 'role:'.$role->name, $role->id, [
            'role'        => $role->name,
            'permissions' => $before,
        ], [
            'role'        => $role->name,
            'permissions' => $after,
        ]);

        return back()->with('success', 'Hak akses untuk role '.$role->name.' berhasil disimpan.');
    }

    /**
     * Copy seluruh permission dari role sumber ke role tujuan.
     */
    public function copy(Request $request, Role $role)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'source_role_id' => ['required', 'integer', 'exists:roles,id'],
        ], [
            'source_role_id.different' => 'Role sumber tidak boleh sama dengan role tujuan.',
        ]);

        abort_if((int) $validated['source_role_id'] === $role->id, 422, 'Role sumber tidak boleh sama dengan role tujuan.');

        if ($role->name === 'super_admin') {
            return back()->with('info', 'Super Admin selalu memiliki seluruh hak akses.');
        }

        $source = Role::findOrFail($validated['source_role_id']);
        $before = $this->permissions->permissionsOfRole($role);

        $this->permissions->copyPermissions($source, $role);

        $after = $this->permissions->permissionsOfRole($role->fresh());

        ActivityLogger::log('copy_menu_permission', 'role:'.$role->name, $role->id, [
            'role'        => $role->name,
            'permissions' => $before,
        ], [
            'role'        => $role->name,
            'copied_from' => $source->name,
            'permissions' => $after,
        ]);

        return back()->with('success', 'Hak akses berhasil disalin dari role '.$source->name.'.');
    }

    /**
     * Reset (kosongkan) seluruh permission sebuah role.
     */
    public function reset(Role $role)
    {
        $this->authorizeManage();

        if ($role->name === 'super_admin') {
            return back()->with('info', 'Super Admin tidak dapat direset.');
        }

        $before = $this->permissions->permissionsOfRole($role);

        $this->permissions->resetPermissions($role);

        ActivityLogger::log('reset_menu_permission', 'role:'.$role->name, $role->id, [
            'role'        => $role->name,
            'permissions' => $before,
        ], [
            'role'        => $role->name,
            'permissions' => [],
        ]);

        return back()->with('success', 'Seluruh hak akses role '.$role->name.' berhasil direset.');
    }

    /**
     * Bangun ulang permission Spatie dari definisi menu (Rebuild Permission).
     */
    public function rebuild()
    {
        $this->authorizeManage();

        $count = $this->permissions->rebuild();

        ActivityLogger::log('rebuild_permission', 'permission:all', 0, null, ['synced' => $count]);

        return back()->with('success', 'Permission berhasil dibangun ulang ('.$count.' aksi disinkronkan).');
    }

    /**
     * Bersihkan cache permission (Clear Permission Cache).
     */
    public function clearCache()
    {
        $this->authorizeManage();

        $this->permissions->clearCache();

        ActivityLogger::log('clear_permission_cache', 'permission:cache');

        return back()->with('success', 'Cache permission berhasil dibersihkan.');
    }

    /**
     * Tampilkan Audit Log khusus perubahan permission.
     */
    public function audit()
    {
        $this->authorizeManage();

        $logs = AuditLog::query()
            ->with('user')
            ->whereIn('action', [
                'update_menu_permission',
                'copy_menu_permission',
                'reset_menu_permission',
                'rebuild_permission',
                'clear_permission_cache',
            ])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('system.permissions.audit', compact('logs'));
    }
}
