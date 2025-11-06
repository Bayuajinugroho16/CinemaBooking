@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Booking Detail #{{ $booking->id }}</h1>

        <!-- Alert Section -->
        @if(!$booking->payment_proof)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-4"></i>
                <div>
                    <h3 class="text-yellow-800 font-bold text-lg">Lengkapi Pembayaran</h3>
                    <p class="text-yellow-700 mt-1">
                        Silakan upload bukti pembayaran untuk melanjutkan proses verifikasi.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Bukti Pembayaran</h2>

            @if($booking->payment_proof)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-800 font-medium">Bukti pembayaran sudah diupload</span>
                    </div>
                    <a href="{{ route('user.booking.payment-proof', $booking->id) }}"
                       target="_blank"
                       class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                       <i class="fas fa-eye mr-1"></i> Lihat Bukti
                    </a>
                </div>
            @endif

            <form action="{{ route('user.booking.upload-proof', $booking->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Bukti Pembayaran
                    </label>
                    <input type="file"
                           name="payment_proof"
                           accept="image/*"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-3 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100
                                  border border-gray-300 rounded-lg"
                           required>
                    <p class="text-xs text-gray-500 mt-2">
                        Format: JPG, PNG, GIF (Maksimal: 2MB)
                    </p>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700
                               transition duration-200 font-semibold flex items-center justify-center">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Bukti Pembayaran
                </button>
            </form>
        </div>

        <!-- Booking Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Booking</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Film</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->film->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Studio</label>
                        <p class="text-gray-900">{{ $booking->studio->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tanggal & Waktu</label>
                        <p class="text-gray-900">
                            {{ \Carbon\Carbon::parse($booking->show_date)->format('d M Y') }}
                            pukul {{ $booking->show_time }}
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Kursi</label>
                        <p class="text-gray-900 font-mono">{{ $booking->seats->pluck('seat_code')->implode(', ') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Total Harga</label>
                        <p class="text-xl font-bold text-green-600">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status Pembayaran</label>
                        <span class="px-3 py-1 text-sm rounded-full
                            {{ $booking->payment_status == 'verified' ? 'bg-green-100 text-green-800' :
                               ($booking->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $booking->payment_proof ? 'Menunggu Verifikasi' : 'Belum Upload Bukti' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
