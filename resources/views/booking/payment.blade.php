@extends('layouts.app')

@section('content')
<div class="container py-4 text-center">
    <h3 class="fw-bold mb-3">💳 Pembayaran Tiket</h3>
    <p>Total yang harus dibayar:</p>
    <h4 class="text-success fw-bold mb-4">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</h4>

    <div class="mb-4">
        <p>Scan QRIS berikut untuk melakukan pembayaran:</p>
        <img src="{{ asset('qris.jpg') }}" alt="QRIS" class="img-fluid border rounded shadow" style="max-width: 250px;">
    </div>

    <form action="{{ route('booking.upload', $booking->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="payment_proof" class="form-label fw-semibold">Upload Bukti Pembayaran</label>
            <input type="file" name="payment_proof" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-success">Upload & Konfirmasi</button>
    </form>
</div>
@endsection
