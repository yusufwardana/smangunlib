@php
    $fields = $fields ?? [];
    $textareas = $textareas ?? [];
    $uploads = $uploads ?? [];
    $checks = $checks ?? [];
    $active = $active ?? false;
@endphp

<div class="tab-pane fade {{ $active ? 'show active' : '' }}" id="tab-{{ $group }}">
    <form action="{{ route('system.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="group" value="{{ $group }}">
        <h5 class="fw-bold mb-3">{{ $title }}</h5>
        <div class="row g-3">
            @foreach($fields as $name => $label)
                <div class="col-md-6">
                    <label class="form-label">{{ $label }}</label>
                    <input type="{{ str_contains($name, 'password') ? 'password' : 'text' }}" name="settings[{{ $name }}]" class="form-control" value="{{ old('settings.'.$name, $settings[$group.'.'.$name] ?? '') }}">
                </div>
            @endforeach
            @foreach($textareas as $name => $label)
                <div class="col-12">
                    <label class="form-label">{{ $label }}</label>
                    <textarea name="settings[{{ $name }}]" rows="4" class="form-control rich-editor">{{ old('settings.'.$name, $settings[$group.'.'.$name] ?? '') }}</textarea>
                </div>
            @endforeach
            @foreach($checks as $name => $label)
                <div class="col-md-4">
                    <div class="form-check form-switch mt-4">
                        <input type="hidden" name="settings[{{ $name }}]" value="0">
                        <input class="form-check-input" type="checkbox" name="settings[{{ $name }}]" value="1" @checked((bool)($settings[$group.'.'.$name] ?? false))>
                        <label class="form-check-label">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
            @foreach($uploads as $name => $label)
                <div class="col-md-4">
                    <label class="form-label">{{ $label }}</label>
                    <input type="file" name="uploads[{{ $name }}]" class="form-control">
                    @if(!empty($settings[$group.'.'.$name]))
                        <a class="small d-inline-block mt-1" target="_blank" href="{{ asset('storage/'.$settings[$group.'.'.$name]) }}">Lihat file saat ini</a>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan</button>
            @isset($testRoute)
                <button formaction="{{ $testRoute }}" class="btn btn-outline-primary">Test {{ str($group)->headline() }}</button>
            @endisset
        </div>
    </form>
</div>
