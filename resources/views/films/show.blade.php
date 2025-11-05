@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center fw-bold">🎞️ Daftar Film yang Sedang Tayang</h2>

    <div class="row">
        @forelse($films as $film)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="{{ asset('storage/'.$film->poster) }}" class="card-img-top" alt="{{ $film->title }}" style="height: 300px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $film->title }}</h5>
                        <p class="text-muted">{{ Str::limit($film->description, 100) }}</p>
                        <p class="fw-bold mb-2">Rp {{ number_format($film->price, 0, ',', '.') }}</p>
                        <a href="{{ route('films.show', $film->id) }}" class="btn btn-primary mt-auto">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">Belum ada film tersedia.</p>
        @endforelse
    </div>
</div>
@endsection
