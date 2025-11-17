<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Saya - CINEMA BOOKING</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-900 text-white">
    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 bg-black shadow-2xl sticky top-0 z-50 border-b border-yellow-500">
        <!-- Logo -->
        <div class="text-2xl font-bold text-white tracking-wider">
            <span class="text-yellow-400 font-black">CINEMA</span>
            <span class="text-white font-normal">BOOKING</span>
        </div>

        <!-- Navigasi -->
        <nav class="flex items-center space-x-6">
            <a href="{{ route('home') }}"
               class="flex items-center text-white hover:text-yellow-400 transition text-sm">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <a href="{{ route('booking.myTicket') }}"
               class="flex items-center text-yellow-400 font-semibold text-sm border-b-2 border-yellow-400 pb-1">
                <i class="fas fa-ticket-alt mr-2"></i> Pesanan Saya
            </a>

            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-500 transition flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> LOGOUT
                </button>
            </form>
        </nav>
    </header>

    <div class="min-h-screen bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold text-white mb-8 flex items-center">
                <i class="fas fa-ticket-alt text-yellow-500 mr-3"></i> Tiket Saya
            </h1>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-green-600 text-white p-4 rounded-lg mb-6 border-l-4 border-green-400">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if($bookings->count() > 0)
                <div class="space-y-6">
                    @foreach($bookings as $booking)
                        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border-l-4
                            {{ $booking->status == 'confirmed' ? 'border-green-500' :
                               ($booking->status == 'pending' ? 'border-yellow-500' : 'border-red-500') }}">

                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h2 class="text-xl font-bold text-white">{{ $booking->film->title }}</h2>
                                    <p class="text-gray-400">{{ $booking->studio->name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 text-sm rounded-full
                                        {{ $booking->payment_status == 'verified' ? 'bg-green-900 text-green-300' :
                                           ($booking->payment_status == 'pending' ? 'bg-yellow-900 text-yellow-300' :
                                           ($booking->payment_proof ? 'bg-blue-900 text-blue-300' : 'bg-gray-700 text-gray-300')) }}">
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
                                    <p class="text-sm text-gray-400 mt-1">
                                        Booking #{{ $booking->id }} •
                                        {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Tanggal & Waktu Tayang</label>
                                    <p class="text-white">
                                        <i class="fas fa-calendar-alt text-yellow-500 mr-2"></i>
                                        {{ \Carbon\Carbon::parse($booking->show_date)->format('d M Y') }}
                                        pukul {{ $booking->show_time }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Kursi</label>
                                    <p class="text-white font-mono">
                                        <i class="fas fa-chair text-yellow-500 mr-2"></i>
                                        {{ $booking->seats->pluck('seat_code')->implode(', ') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Total Pembayaran</label>
                                    <p class="text-lg font-bold text-green-400">
                                        <i class="fas fa-tag text-yellow-500 mr-2"></i>
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <!-- Tombol Lihat Detail -->
                                <a href="{{ route('user.booking.show', $booking->id) }}"
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition text-sm flex items-center">
                                    <i class="fas fa-eye mr-2"></i> Lihat Detail
                                </a>

                                <!-- Status-based Actions -->
                                @if($booking->payment_status == 'verified')
                                    <span class="bg-green-900 text-green-300 px-4 py-2 rounded-lg text-sm flex items-center">
                                        <i class="fas fa-check mr-2"></i> Tiket Aktif
                                    </span>
                                    <a href="#"
                                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-500 transition text-sm flex items-center">
                                        <i class="fas fa-download mr-2"></i> Download Tiket
                                    </a>
                                @elseif($booking->payment_status == 'pending' && !$booking->payment_proof)
                                    <a href="{{ route('booking.payment', $booking->id) }}"
                                       class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-500 transition text-sm flex items-center">
                                        <i class="fas fa-upload mr-2"></i> Upload Bukti Bayar
                                    </a>
                                @elseif($booking->payment_status == 'pending' && $booking->payment_proof)
                                    <span class="bg-blue-900 text-blue-300 px-4 py-2 rounded-lg text-sm flex items-center">
                                        <i class="fas fa-clock mr-2"></i> Menunggu Verifikasi
                                    </span>
                                    <a href="{{ route('user.booking.payment-proof', $booking->id) }}"
                                       target="_blank"
                                       class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-sm flex items-center">
                                        <i class="fas fa-file-invoice mr-2"></i> Lihat Bukti
                                    </a>
                                @elseif($booking->payment_status == 'rejected')
                                    <span class="bg-red-900 text-red-300 px-4 py-2 rounded-lg text-sm flex items-center">
                                        <i class="fas fa-times mr-2"></i> Pembayaran Ditolak
                                    </span>
                                @endif
                            </div>

                            <!-- Admin Notes (if any) -->
                            @if($booking->admin_notes && $booking->payment_status == 'rejected')
                                <div class="mt-3 p-3 bg-red-900/30 rounded-lg border border-red-800">
                                    <p class="text-red-300 text-sm">
                                        <strong><i class="fas fa-info-circle mr-2"></i>Catatan Admin:</strong> {{ $booking->admin_notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-8 text-center border border-gray-700">
                    <i class="fas fa-ticket-alt text-gray-500 text-6xl mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-300 mb-2">Belum Ada Tiket</h2>
                    <p class="text-gray-500 mb-6">Anda belum memiliki tiket aktif. Yuk pesan tiket film favorit Anda!</p>
                    <a href="{{ route('home') }}"
                       class="bg-yellow-500 text-gray-900 px-6 py-3 rounded-lg hover:bg-yellow-400 transition font-semibold inline-flex items-center">
                        <i class="fas fa-film mr-2"></i> Pesan Tiket Sekarang
                    </a>
                </div>
            @endif

            <!-- Pagination or Info -->
            @if($bookings->count() > 0)
                <div class="mt-6 text-center text-gray-400 text-sm">
                    Menampilkan {{ $bookings->count() }} tiket
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black border-t border-gray-800 text-center text-sm text-gray-400 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-yellow-400 font-bold mb-4">TENTANG KAMI</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-yellow-400 transition">Profil Perusahaan</a></li>
                        <li><a href="#" class="hover:text-yellow-400 transition">XXI Premiere</a></li>
                        <li><a href="#" class="hover:text-yellow-400 transition">The Premiere</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-yellow-400 font-bold mb-4">BANTUAN</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-yellow-400 transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-yellow-400 transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-yellow-400 transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-yellow-400 font-bold mb-4">HUBUNGI KAMI</h3>
                    <ul class="space-y-2">
                        <li><i class="fas fa-phone mr-2"></i> 1500 123</li>
                        <li><i class="fas fa-envelope mr-2"></i> help@cinemaxxi.com</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-yellow-400 font-bold mb-4">FOLLOW KAMI</h3>
                    <div class="flex space-x-4 justify-center md:justify-start">
                        <a href="#" class="text-gray-400 hover:text-yellow-400 transition"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-yellow-400 transition"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-yellow-400 transition"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6">
                <p>&copy; {{ date('Y') }} Cinema XXI. All rights reserved.</p>
                <p class="text-xs mt-2">Didesain dengan <span class="text-red-500">❤</span> menggunakan Tailwind CSS</p>
            </div>
        </div>
    </footer>
</body>
</html>


