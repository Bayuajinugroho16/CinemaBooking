@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Tiket Saya</h1>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($bookings->count() > 0)
            <div class="space-y-6">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4
                        {{ $booking->status == 'confirmed' ? 'border-green-500' :
                           ($booking->status == 'pending' ? 'border-yellow-500' : 'border-red-500') }}">

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $booking->film->title }}</h2>
                                <p class="text-gray-600">{{ $booking->studio->name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-sm rounded-full
                                    {{ $booking->payment_status == 'verified' ? 'bg-green-100 text-green-800' :
                                       ($booking->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($booking->payment_proof ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                    @if($booking->payment_status == 'verified')
                                        Terverifikasi
                                    @elseif($booking->payment_status == 'pending' && $booking->payment_proof)
                                        Menunggu Verifikasi
                                    @elseif($booking->payment_status == 'pending' && !$booking->payment_proof)
                                        Belum Upload Bukti
                                    @else
                                        Ditolak
                                    @endif
                                </span>
                                <p class="text-sm text-gray-500 mt-1">
                                    Booking #{{ $booking->id }} •
                                    {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Tanggal & Waktu Tayang</label>
                                <p class="text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->show_date)->format('d M Y') }}
                                    pukul {{ $booking->show_time }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Kursi</label>
                                <p class="text-gray-900 font-mono">{{ $booking->seats->pluck('seat_code')->implode(', ') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Total Pembayaran</label>
                                <p class="text-lg font-bold text-green-600">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <!-- Tombol Lihat Detail -->
                            <a href="{{ route('user.booking.show', $booking->id) }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm flex items-center">
                                <i class="fas fa-eye mr-2"></i> Lihat Detail
                            </a>

                            <!-- Status-based Actions -->
                            @if($booking->payment_status == 'verified')
                                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm flex items-center">
                                    <i class="fas fa-check mr-2"></i> Tiket Aktif
                                </span>
                                <a href="#"
                                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm flex items-center">
                                    <i class="fas fa-download mr-2"></i> Download Tiket
                                </a>
                            @elseif($booking->payment_status == 'pending' && !$booking->payment_proof)
                                <a href="{{ route('booking.payment', $booking->id) }}"
                                   class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition text-sm flex items-center">
                                    <i class="fas fa-upload mr-2"></i> Upload Bukti Bayar
                                </a>
                            @elseif($booking->payment_status == 'pending' && $booking->payment_proof)
                                <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg text-sm flex items-center">
                                    <i class="fas fa-clock mr-2"></i> Menunggu Verifikasi
                                </span>
                                <a href="{{ route('user.booking.payment-proof', $booking->id) }}"
                                   target="_blank"
                                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm flex items-center">
                                    <i class="fas fa-file-invoice mr-2"></i> Lihat Bukti
                                </a>
                            @elseif($booking->payment_status == 'rejected')
                                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-lg text-sm flex items-center">
                                    <i class="fas fa-times mr-2"></i> Pembayaran Ditolak
                                </span>
                                @if($booking->admin_notes)
                                    <span class="text-red-600 text-sm">
                                        Alasan: {{ $booking->admin_notes }}
                                    </span>
                                @endif
                            @endif
                        </div>

                        <!-- Admin Notes (if any) -->
                        @if($booking->admin_notes && $booking->payment_status == 'rejected')
                            <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                <p class="text-red-700 text-sm">
                                    <strong>Catatan Admin:</strong> {{ $booking->admin_notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <i class="fas fa-ticket-alt text-gray-400 text-6xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Tiket</h2>
                <p class="text-gray-500 mb-6">Anda belum memiliki tiket aktif. Yuk pesan tiket film favorit Anda!</p>
                <a href="{{ route('home') }}"
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold inline-flex items-center">
                    <i class="fas fa-film mr-2"></i> Pesan Tiket Sekarang
                </a>
            </div>
        @endif

        <!-- Pagination or Info -->
        @if($bookings->count() > 0)
            <div class="mt-6 text-center text-gray-500 text-sm">
                Menampilkan {{ $bookings->count() }} tiket
            </div>
        @endif
    </div>
</div>
@endsection
