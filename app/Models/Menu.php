<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;

/**
 * Model Menu.
 *
 * Merepresentasikan satu node pada pohon menu (tree) yang seluruhnya
 * dikontrol melalui database untuk kebutuhan RBAC (Role Based Access Control).
 * Sebuah menu dapat memiliki submenu (children) tanpa batas kedalaman.
 *
 * @property int         $id
 * @property int|null    $parent_id
 * @property string      $key
 * @property string      $title
 * @property string|null $icon
 * @property string|null $route
 * @property string|null $url
 * @property int         $sort
 * @property bool        $is_active
 */
class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'parent_id',
        'key',
        'title',
        'icon',
        'route',
        'url',
        'sort',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort'      => 'integer',
        ];
    }

    /**
     * Menu induk (null jika menu level teratas).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Submenu (children) terurut berdasarkan kolom sort.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    /**
     * Rekursif: children beserta seluruh keturunannya (untuk membangun tree).
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive', 'menuPermissions');
    }

    /**
     * Daftar aksi/hak akses yang tersedia untuk menu ini (view, create, dst).
     */
    public function menuPermissions(): HasMany
    {
        return $this->hasMany(MenuPermission::class);
    }

    /**
     * Seluruh permission Spatie yang dipetakan ke menu ini
     * (melalui tabel pivot permission_has_menu).
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_has_menu',
            'menu_id',
            'permission_id'
        )->withPivot('action');
    }

    /**
     * Nama permission Spatie untuk sebuah aksi pada menu ini.
     * Contoh: menu "koleksi.buku" + aksi "create" => "koleksi.buku.create".
     */
    public function permissionName(string $action): string
    {
        return $this->key.'.'.$action;
    }

    /**
     * Scope: hanya menu yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: hanya menu level teratas (tanpa induk).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
