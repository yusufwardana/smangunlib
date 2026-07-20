{{--
    Partial rekursif satu node pohon menu untuk matriks hak akses.

    Variabel:
      $menu    : instance App\Models\Menu (dengan childrenRecursive & menuPermissions ter-eager-load)
      $level   : kedalaman (0 = root) untuk indentasi
      $granted : array nama permission yang dimiliki role terpilih
      $actions : peta [action => label] seluruh aksi sistem
--}}
<div class="menu-node level-{{ $level }}">
    <div class="menu-row px-3 py-2 d-flex flex-wrap align-items-center justify-content-between gap-2"
         style="padding-left: {{ 0.75 + $level * 1.25 }}rem !important;">
        <div class="menu-title">
            @if($level > 0)
                <i class="fa-solid fa-angle-right text-muted me-1"></i>
            @endif
            @if($menu->icon)
                <i class="{{ $menu->icon }} me-1 text-primary"></i>
            @endif
            {{ $menu->title }}
            <code class="text-muted small ms-1">{{ $menu->key }}</code>
        </div>

        <div class="menu-actions">
            @foreach ($menu->menuPermissions->sortBy(fn ($p) => array_search($p->action, array_keys($actions))) as $mp)
                @php $permName = $menu->key.'.'.$mp->action; @endphp
                <div class="form-check form-check-inline">
                    <input class="form-check-input action-checkbox"
                           type="checkbox"
                           name="permissions[]"
                           value="{{ $permName }}"
                           id="perm-{{ Str::slug($permName) }}"
                           data-action="{{ $mp->action }}"
                           {{ in_array($permName, $granted, true) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="perm-{{ Str::slug($permName) }}">
                        {{ $mp->label ?? ($actions[$mp->action] ?? ucfirst($mp->action)) }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    @if($menu->children->isNotEmpty())
        <div class="submenu">
            @foreach ($menu->children as $child)
                @include('system.permissions.partials.menu-node', ['menu' => $child, 'level' => $level + 1, 'granted' => $granted, 'actions' => $actions])
            @endforeach
        </div>
    @endif
</div>
