<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model MenuPermission.
 *
 * Mendefinisikan aksi/hak akses yang tersedia untuk sebuah menu, misalnya
 * view, create, edit, delete, approve, export_pdf, export_excel, import_excel,
 * print, download, upload. Data inilah yang menggerakkan matriks checkbox di UI
 * pengaturan hak akses.
 *
 * @property int    $id
 * @property int    $menu_id
 * @property string $action
 * @property string|null $label
 */
class MenuPermission extends Model
{
    protected $table = 'menu_permissions';

    protected $fillable = [
        'menu_id',
        'action',
        'label',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
