<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi - Cinema XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Success Icon -->
            <div class="text-green-400 text-6xl mb-6">
                <i class="fas fa-clock"></i>
            </div>

            <h1 class="text-3xl font-bold text-yellow-400 mb-4">Menunggu Verifikasi</h1>
            <p class="text-gray-300 text-lg mb-6">
                Bukti pembayaran Anda telah berhasil diupload dan sedang menunggu verifikasi dari admin.
            </p>

            <!-- Booking Info -->
            <div class="bg-gray-800 rounded-xl p-6 mb-6 text-left">
                <h2 class="text-xl font-bold text-yellow-400 mb-4">Detail Booking</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-300"><strong>Kode Booking:</strong> #{{ $booking->id }}</p>
                        <p class="text-gray-300"><strong>Film:</strong> {{ $booking->film->title }}</p>
                        <p class="text-gray-300"><strong>Studio:</strong> {{ $booking->studio->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-300"><strong>Tanggal:</strong> {{ $booking->show_date }}</p>
                        <p class="text-gray-300"><strong>Jam:</strong> {{ $booking->show_time }}</p>
                        <p class="text-gray-300"><strong>Kursi:</strong>
                            {{ $booking->seats->pluck('seat_code')->implode(', ') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-yellow-500 bg-opacity-20 rounded-lg">
                    <p class="text-yellow-400">
                        <i class="fas fa-info-circle mr-2"></i>
                        Status: Menunggu verifikasi pembayaran
                    </p>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-gray-800 rounded-xl p-6 mb-6">
                <h2 class="text-xl font-bold text-yellow-400 mb-4">Apa Selanjutnya?</h2>
                <div class="space-y-3 text-left">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white font-bold">1</span>
                        </div>
                        <p class="text-gray-300">Admin akan memverifikasi bukti pembayaran Anda</p>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white font-bold">2</span>
                        </div>
                        <p class="text-gray-300">Anda akan mendapatkan notifikasi via email/SMS</p>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white font-bold">3</span>
                        </div>
                        <p class="text-gray-300">Tiket dapat diambil di loket bioskop</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="space-y-4">
                <a href="{{ route('home') }}"
                   class="inline-block bg-yellow-500 text-black px-6 py-3 rounded-lg font-bold hover:bg-yellow-400 transition">
                    <i class="fas fa-home mr-2"></i>Kembali ke Home
                </a>
                <br>
                <a href="{{ route('booking.myTicket') }}"
                   class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-400 transition">
                    <i class="fas fa-ticket-alt mr-2"></i>Lihat Tiket Saya
                </a>
            </div>
        </div>
    </div>
</body>
</html>
