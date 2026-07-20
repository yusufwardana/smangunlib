{{--
    Reusable field renderer untuk Theme Manager.

    Parameter yang diharapkan:
      $group    string  nama grup (mis. "color")
      $key      string  nama key (mis. "primary_color")
      $type     string  string|color|image|css|js|boolean|select
      $label    string  label tampilan
      $value    mixed   nilai saat ini
      $options  array   [nilai => label] khusus type=select (opsional)
--}}
@php
    $inputName = "settings[$key]";
    $fieldId = "theme_{$group}_{$key}";
    $dataAttr = "data-theme-key=\"$group.$key\"";
@endphp

<div class="col-md-6 mb-3">
    <label for="{{ $fieldId }}" class="form-label fw-semibold small text-uppercase text-muted">{{ $label }}</label>

    @switch($type)
        @case('color')
            <div class="input-group">
                <input type="color"
                       class="form-control form-control-color theme-color-picker"
                       id="{{ $fieldId }}"
                       value="{{ $value ?: '#000000' }}"
                       data-target="#{{ $fieldId }}_text"
                       {!! $dataAttr !!}
                       title="Pilih warna">
                <input type="text"
                       class="form-control theme-color-text"
                       id="{{ $fieldId }}_text"
                       name="{{ $inputName }}"
                       value="{{ $value }}"
                       data-target="#{{ $fieldId }}"
                       {!! $dataAttr !!}
                       placeholder="#000000">
            </div>
            @break

        @case('image')
            <div>
                <input type="file"
                       class="form-control theme-image-input"
                       id="{{ $fieldId }}"
                       name="uploads[{{ $key }}]"
                       accept=".png,.jpg,.jpeg,.svg,.webp,.ico,.mp4,.webm"
                       data-preview="#{{ $fieldId }}_preview">
                <div class="mt-2">
                    @if($value)
                        <img id="{{ $fieldId }}_preview" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($value) }}"
                             alt="{{ $label }}" class="img-thumbnail theme-preview-img" style="max-height: 80px;">
                    @else
                        <img id="{{ $fieldId }}_preview" src="" alt="" class="img-thumbnail theme-preview-img d-none" style="max-height: 80px;">
                    @endif
                </div>
                <small class="text-muted">Maks 5 MB. PNG, JPG, SVG, WEBP, ICO.</small>
            </div>
            @break

        @case('boolean')
            <div class="form-check form-switch">
                <input type="hidden" name="{{ $inputName }}" value="0">
                <input type="checkbox"
                       class="form-check-input theme-toggle"
                       id="{{ $fieldId }}"
                       name="{{ $inputName }}"
                       value="1"
                       {{ filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                       {!! $dataAttr !!}>
                <label class="form-check-label" for="{{ $fieldId }}">Aktifkan</label>
            </div>
            @break

        @case('select')
            <select class="form-select theme-select"
                    id="{{ $fieldId }}"
                    name="{{ $inputName }}"
                    {!! $dataAttr !!}>
                @foreach($options ?? [] as $optValue => $optLabel)
                    <option value="{{ $optValue }}" {{ (string) $value === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
                @endforeach
            </select>
            @break

        @case('css')
        @case('js')
            <textarea class="form-control font-monospace theme-code"
                      id="{{ $fieldId }}"
                      name="{{ $inputName }}"
                      rows="8"
                      spellcheck="false"
                      placeholder="{{ $type === 'css' ? '/* Tulis CSS custom di sini */' : '// Tulis JavaScript custom di sini' }}">{{ $value }}</textarea>
            @break

        @default
            <input type="text"
                   class="form-control theme-text"
                   id="{{ $fieldId }}"
                   name="{{ $inputName }}"
                   value="{{ $value }}"
                   {!! $dataAttr !!}>
    @endswitch
</div>
