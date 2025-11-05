@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">🎫 Tiket Saya</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Film</th>
                <th>Tanggal</th>
                <th>Kursi</th>
                <th>Total</th>
                <th>Status</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $booking->screening->film->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->screening->date)->format('d M Y') }} {{ \Carbon\Carbon::parse($booking->screening->time)->format('H:i') }}</td>
                    <td>{{ implode(', ', json_decode($booking->seats)) }}</td>
                    <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($booking->status == 'pending')
                            <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                        @elseif($booking->status == 'paid')
                            <span class="badge bg-info text-dark">Menunggu Konfirmasi Admin</span>
                        @else
                            <span class="badge bg-success">Terkonfirmasi</span>
                        @endif
                    </td>
                    <td>
                        @if($booking->payment_proof)
                            <a href="{{ asset('storage/'.$booking->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">Belum ada tiket.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
