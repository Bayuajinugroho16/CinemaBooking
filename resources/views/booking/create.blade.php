@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 fw-bold">🎟️ Booking Kursi - {{ $screening->film->title }}</h3>
    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($screening->date)->format('d M Y') }},
       <strong>Jam:</strong> {{ \Carbon\Carbon::parse($screening->time)->format('H:i') }}</p>

    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="screening_id" value="{{ $screening->id }}">

        <div class="mb-3">
            <label class="form-label">Pilih Kursi (tekan Ctrl/Cmd untuk pilih lebih dari satu)</label>
            <select name="seats[]" class="form-select" multiple required>
                @foreach(range('A', 'E') as $row)
                    @foreach(range(1, 6) as $num)
                        <option value="{{ $row.$num }}">{{ $row.$num }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" name="phone" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea class="form-control" name="address" rows="2" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Konfirmasi Booking</button>
    </form>
</div>
@endsection
