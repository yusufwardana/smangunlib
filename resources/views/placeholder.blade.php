@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container py-5 text-center">
    <i class="fa-solid fa-tools fa-4x text-muted mb-4"></i>
    <h2 class="fw-bold">{{ $title }}</h2>
    <p class="text-muted">Fitur ini sedang dalam pengembangan dan akan segera tersedia.</p>
</div>
@endsection