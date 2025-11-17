@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white flex items-center">
                <i class="fas fa-ticket-alt text-yellow-500 mr-3"></i>
                Detail Booking #{{ $booking->id }}
            </h1>
            <a href="{{ route('booking.myTicket') }}"
               class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-green-900/30 border border-green-700 text-green-300 p-4 rounded-xl mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-900/30 border border-red-700 text-red-300 p-4 rounded-xl mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Status Alert -->
        @if($booking->payment_status == 'verified')
        <div class="bg-green-900/30 border border-green-700 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-green-300 font-bold text-lg">Pembayaran Terverifikasi</h3>
                    <p class="text-green-200 mt-1">
                        Tiket Anda sudah aktif dan siap digunakan. Silakan download tiket untuk ditunjukkan di bioskop.
                    </p>
                </div>
            </div>
        </div>
        @elseif($booking->payment_status == 'pending' && $booking->payment_proof)
        <div class="bg-yellow-900/30 border border-yellow-700 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <i class="fas fa-clock text-yellow-400 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-yellow-300 font-bold text-lg">Menunggu Verifikasi</h3>
                    <p class="text-yellow-200 mt-1">
                        Bukti pembayaran Anda sedang menunggu verifikasi dari admin.
                    </p>
                </div>
            </div>
        </div>
        @elseif($booking->payment_status == 'pending' && !$booking->payment_proof)
        <div class="bg-yellow-900/30 border border-yellow-700 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-yellow-300 font-bold text-lg">Lengkapi Pembayaran</h3>
                    <p class="text-yellow-200 mt-1">
                        Silakan upload bukti pembayaran untuk melanjutkan proses verifikasi.
                    </p>
                </div>
            </div>
        </div>
        @elseif($booking->payment_status == 'rejected')
        <div class="bg-red-900/30 border border-red-700 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <i class="fas fa-times-circle text-red-400 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-red-300 font-bold text-lg">Pembayaran Ditolak</h3>
                    <p class="text-red-200 mt-1">
                        Bukti pembayaran Anda ditolak. Silakan upload ulang bukti pembayaran yang valid.
                    </p>
                    @if($booking->admin_notes)
                    <div class="mt-3 p-3 bg-red-800/50 rounded-lg">
                        <p class="text-red-200 text-sm">
                            <strong>Catatan Admin:</strong> {{ $booking->admin_notes }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Booking Information -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700 mb-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center">
                <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                Informasi Booking
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Film Information -->
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        @if($booking->film->image)
                            <img src="{{ asset('storage/' . $booking->film->image) }}"
                                 alt="{{ $booking->film->title }}"
                                 class="w-20 h-28 object-cover rounded-lg">
                        @else
                            <div class="w-20 h-28 bg-gray-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-film text-gray-500"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $booking->film->title }}</h3>
                            <p class="text-yellow-400 text-sm mt-1">{{ $booking->film->genre ?? 'Genre tidak tersedia' }}</p>
                            <p class="text-gray-400 text-xs mt-2">{{ Str::limit($booking->film->description, 80) }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400">
                                <i class="fas fa-calendar-alt mr-2"></i>Tanggal & Waktu Tayang
                            </label>
                            <p class="text-white font-semibold">
                                {{ \Carbon\Carbon::parse($booking->show_date)->format('d M Y') }}
                                pukul {{ $booking->show_time }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400">
                                <i class="fas fa-building mr-2"></i>Studio
                            </label>
                            <p class="text-white">{{ $booking->studio->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="space-y-4">
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            <i class="fas fa-chair mr-2"></i>Kursi yang Dipilih
                        </label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($booking->seats as $seat)
                                <span class="bg-yellow-500 text-gray-900 px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $seat->seat_code }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-400">
                            <i class="fas fa-tag mr-2"></i>Total Pembayaran
                        </label>
                        <p class="text-2xl font-bold text-green-400 mt-1">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-400 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Status Pembayaran
                        </label>
                        <span class="px-3 py-1 text-sm rounded-full font-semibold
                            {{ $booking->payment_status == 'verified' ? 'bg-green-900 text-green-300' :
                               ($booking->payment_status == 'pending' ? 'bg-yellow-900 text-yellow-300' :
                               'bg-red-900 text-red-300') }}">
                            @if($booking->payment_status == 'verified')
                                <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                            @elseif($booking->payment_status == 'pending' && $booking->payment_proof)
                                <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                            @elseif($booking->payment_status == 'pending' && !$booking->payment_proof)
                                <i class="fas fa-upload mr-1"></i> Belum Upload Bukti
                            @else
                                <i class="fas fa-times-circle mr-1"></i> Ditolak
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-cogs text-yellow-500 mr-3"></i>
                Aksi
            </h2>

            <div class="flex flex-wrap gap-4">
                @if($booking->payment_status == 'verified')
                    <a href="#"
                       class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-500 transition font-semibold flex items-center">
                        <i class="fas fa-download mr-2"></i> Download Tiket
                    </a>
                    <span class="bg-green-900 text-green-300 px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-check mr-2"></i> Tiket Aktif
                    </span>
                @elseif($booking->payment_status == 'pending' && !$booking->payment_proof)
                    <a href="{{ route('booking.payment', $booking->id) }}"
                       class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-500 transition font-semibold flex items-center">
                        <i class="fas fa-upload mr-2"></i> Upload Bukti Bayar
                    </a>
                @elseif($booking->payment_status == 'pending' && $booking->payment_proof)
                    <span class="bg-blue-900 text-blue-300 px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-clock mr-2"></i> Menunggu Verifikasi
                    </span>
                    <a href="{{ route('user.booking.payment-proof', $booking->id) }}"
                       target="_blank"
                       class="bg-gray-700 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition font-semibold flex items-center">
                        <i class="fas fa-file-invoice mr-2"></i> Lihat Bukti Upload
                    </a>
                @elseif($booking->payment_status == 'rejected')
                    <a href="{{ route('booking.payment', $booking->id) }}"
                       class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-500 transition font-semibold flex items-center">
                        <i class="fas fa-upload mr-2"></i> Upload Ulang Bukti
                    </a>
                    <a href="{{ route('user.booking.payment-proof', $booking->id) }}"
                       target="_blank"
                       class="bg-gray-700 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition font-semibold flex items-center">
                        <i class="fas fa-eye mr-2"></i> Lihat Bukti Sebelumnya
                    </a>
                @endif

                <a href="{{ route('booking.myTicket') }}"
                   class="bg-gray-700 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition font-semibold flex items-center">
                    <i class="fas fa-list mr-2"></i> Kembali ke Daftar Tiket
                </a>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 text-center text-gray-400 text-sm">
            <p>Booking dibuat pada {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y H:i') }}</p>
            @if($booking->payment_proof && $booking->updated_at != $booking->created_at)
                <p class="mt-1">Terakhir diperbarui {{ \Carbon\Carbon::parse($booking->updated_at)->format('d M Y H:i') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
