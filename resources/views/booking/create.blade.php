@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 fw-bold">🎟️ Booking Kursi - {{ $screening->film->title }}</h3>

    <!-- INFO FILM -->
    <div class="card mb-4">
        <div class="card-body">
            <p class="mb-1"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($screening->date)->format('d M Y') }}</p>
            <p class="mb-1"><strong>Jam:</strong> {{ \Carbon\Carbon::parse($screening->time)->format('H:i') }}</p>
            <p class="mb-0"><strong>Harga per kursi:</strong> Rp 35,000</p>
        </div>
    </div>

    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="screening_id" value="{{ $screening->id }}">
        <input type="hidden" name="seats" id="selectedSeats" value="">

        <!-- SEAT SELECTION -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pilih Kursi</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-dark text-white py-2 rounded">
                        <strong>LAYAR</strong>
                    </div>
                </div>

                <div id="seatGrid" class="text-center">
                    @foreach(range('A', 'E') as $row)
                        <div class="mb-2">
                            @foreach(range(1, 6) as $num)
                                @php
                                    $seat = $row.$num;
                                    $booked = in_array($seat, $bookedSeats ?? []);
                                @endphp

                                @if($booked)
                                    <button type="button" class="btn btn-danger btn-sm m-1" disabled>
                                        {{ $seat }}
                                    </button>
                                @else
                                    <button type="button"
                                            class="seat-btn btn btn-outline-primary btn-sm m-1"
                                            data-seat="{{ $seat }}">
                                        {{ $seat }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <span class="badge bg-primary">Biru</span> = Dipilih &nbsp;|&nbsp;
                        <span class="badge bg-danger">Merah</span> = Terbooking
                    </small>
                </div>
            </div>
        </div>

        <!-- SELECTED SEATS SUMMARY -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Detail Pemesanan</h5>
            </div>
            <div class="card-body">
                <div id="selectedInfo">
                    <p class="text-muted mb-2">Belum ada kursi dipilih</p>
                    <p class="mb-0"><strong>Total: Rp 0</strong></p>
                </div>
            </div>
        </div>

        <!-- CUSTOMER INFO -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Data Pemesan</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor Telepon *</label>
                    <input type="text" class="form-control" name="phone" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat *</label>
                    <textarea class="form-control" name="address" rows="2" required></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn" disabled>
            <i class="fas fa-ticket-alt me-2"></i>Konfirmasi Booking
        </button>
    </form>
</div>

<!-- SIMPLE JAVASCRIPT - PASTI WORK -->
<script>
// Tunggu sampai halaman fully loaded
window.addEventListener('load', function() {
    console.log('✅ Page loaded, initializing seat selection...');

    const seatButtons = document.querySelectorAll('.seat-btn');
    const selectedSeatsInput = document.getElementById('selectedSeats');
    const selectedInfo = document.getElementById('selectedInfo');
    const submitBtn = document.getElementById('submitBtn');

    const seatPrice = 35000;
    let selectedSeats = [];

    console.log('Found', seatButtons.length, 'available seats');

    // Fungsi untuk update tampilan
    function updateSelection() {
        console.log('Selected seats:', selectedSeats);

        // Update hidden input
        selectedSeatsInput.value = selectedSeats.join(',');

        // Update display
        if (selectedSeats.length === 0) {
            selectedInfo.innerHTML = `
                <p class="text-muted mb-2">Belum ada kursi dipilih</p>
                <p class="mb-0"><strong>Total: Rp 0</strong></p>
            `;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-ticket-alt me-2"></i>Konfirmasi Booking';
        } else {
            const total = selectedSeats.length * seatPrice;
            selectedInfo.innerHTML = `
                <p class="mb-1"><strong>Kursi dipilih:</strong> ${selectedSeats.join(', ')}</p>
                <p class="mb-1"><strong>Jumlah kursi:</strong> ${selectedSeats.length}</p>
                <p class="mb-0"><strong>Total: Rp ${total.toLocaleString('id-ID')}</strong></p>
            `;
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="fas fa-ticket-alt me-2"></i>Bayar Rp ${total.toLocaleString('id-ID')}`;
        }
    }

    // Add click event to each seat button
    seatButtons.forEach(button => {
        button.addEventListener('click', function() {
            const seat = this.getAttribute('data-seat');
            console.log('Clicked:', seat);

            // Check if already selected
            const index = selectedSeats.indexOf(seat);

            if (index > -1) {
                // Remove from selection
                selectedSeats.splice(index, 1);
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                console.log('Removed:', seat);
            } else {
                // Add to selection
                selectedSeats.push(seat);
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                console.log('Added:', seat);
            }

            updateSelection();
        });
    });

    // Initialize
    updateSelection();
    console.log('🎬 Seat selection ready!');
});
</script>

<style>
.seat-btn {
    width: 45px;
    height: 45px;
    transition: all 0.2s;
}

.seat-btn:hover {
    transform: scale(1.1);
}
</style>
@endsection
