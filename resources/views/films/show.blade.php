@extends('layouts.app')

@section('title', $film->title)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('films.index') }}">Films</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $film->title }}</li>
        </ol>
    </nav>

    <!-- Film Detail Section -->
    <div class="row">
        <!-- Film Poster -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <img src="{{ asset('storage/'.$film->image) }}"
                     class="card-img-top rounded"
                     alt="{{ $film->title }}"
                     style="height: 500px; object-fit: cover;">
            </div>
        </div>

        <!-- Film Information -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <!-- Film Title & Basic Info -->
                    <h1 class="card-title fw-bold text-dark mb-2">{{ $film->title }}</h1>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-primary fs-6">{{ $film->genre }}</span>
                        <span class="text-muted"><i class="fas fa-clock me-1"></i>{{ $film->duration }}</span>
                        <span class="badge bg-{{ $film->status == 'playing' ? 'success' : 'warning' }} fs-6">
                            <i class="fas fa-{{ $film->status == 'playing' ? 'play' : 'clock' }} me-1"></i>
                            {{ $film->status == 'playing' ? 'Sedang Tayang' : 'Akan Tayang' }}
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <h3 class="text-primary fw-bold">Rp {{ number_format($film->price, 0, ',', '.') }}</h3>
                        <small class="text-muted">Harga per tiket</small>
                    </div>

                    <!-- Synopsis -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fas fa-film me-2"></i>Sinopsis
                        </h5>
                        <p class="card-text lh-lg" style="text-align: justify;">{{ $film->description }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-auto">
                        <div class="d-flex flex-column gap-3">
                            @if($film->status == 'playing')
                            <a href="{{ route('bookings.create', $film->id) }}"
                               class="btn btn-primary btn-lg py-3 fw-bold">
                                <i class="fas fa-ticket-alt me-2"></i>Pesan Tiket Sekarang
                            </a>
                            @else
                            <button class="btn btn-secondary btn-lg py-3 fw-bold" disabled>
                                <i class="fas fa-clock me-2"></i>Segera Tayang - Nantikan!
                            </button>
                            @endif

                            <div class="d-flex gap-2">
                                <a href="{{ route('films.index') }}" class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Film
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-dark flex-fill">
                                    <i class="fas fa-home me-2"></i>Kembali ke Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
}
.btn {
    border-radius: 10px;
    transition: all 0.3s ease;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.breadcrumb {
    background: transparent;
    padding: 0;
}
</style>
@endsection
