<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Cinema XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-yellow-400">Pembayaran Tiket</h1>
                <p class="text-gray-300 mt-2">Silakan lakukan pembayaran dan upload bukti</p>
            </div>

            <!-- Booking Info -->
            <div class="bg-gray-800 rounded-xl p-6 mb-6">
                <h2 class="text-xl font-bold text-yellow-400 mb-4">Detail Booking</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-300"><strong>Film:</strong> {{ $booking->film->title }}</p>
                        <p class="text-gray-300"><strong>Studio:</strong> {{ $booking->studio->name }}</p>
                        <p class="text-gray-300"><strong>Tanggal:</strong> {{ $booking->show_date }}</p>
                    </div>
                    <div>
                        <p class="text-gray-300"><strong>Jam:</strong> {{ $booking->show_time }}</p>
                        <p class="text-gray-300"><strong>Kursi:</strong>
                            {{ $booking->seats->pluck('seat_code')->implode(', ') }}
                        </p>
                        <p class="text-2xl font-bold text-green-400">
                            Total: Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- QRIS Payment -->
            <div class="bg-gray-800 rounded-xl p-6 mb-6">
                <h2 class="text-xl font-bold text-yellow-400 mb-4">Bayar dengan QRIS</h2>
                <div class="text-center">
                    <div class="bg-white p-4 rounded-lg inline-block mb-4">
                        <!-- QR Code Placeholder -->
                        <div class="w-64 h-64 bg-gray-200 flex items-center justify-center text-gray-600">
                            <div class="text-center">
                                <i class="fas fa-qrcode text-4xl mb-2"></i>
                                <p>QR Code Pembayaran</p>
                                <small>Scan dengan aplikasi e-wallet</small>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-2">Scan QR code di atas dengan aplikasi e-wallet Anda</p>
                    <p class="text-gray-300">Atau gunakan kode: <code class="bg-gray-700 px-2 py-1 rounded">{{ substr($qrisCode, 0, 50) }}...</code></p>
                </div>
            </div>

            <!-- Upload Proof -->
            <div class="bg-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-bold text-yellow-400 mb-4">Upload Bukti Pembayaran</h2>
                <form action="{{ route('booking.uploadPayment', $booking->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-300 mb-2">Pilih file bukti pembayaran</label>
                        <input type="file" name="payment_proof"
                               class="w-full p-3 bg-gray-700 border border-gray-600 rounded-lg text-white"
                               accept="image/*" required>
                        <p class="text-gray-400 text-sm mt-1">Format: JPG, PNG, GIF (max 2MB)</p>
                    </div>
                    <button type="submit"
                            class="w-full bg-yellow-500 text-black py-3 rounded-lg font-bold hover:bg-yellow-400 transition">
                        <i class="fas fa-upload mr-2"></i>Upload Bukti Pembayaran
                    </button>
                </form>
            </div>

            <!-- Navigation -->
            <div class="text-center mt-6">
                <a href="{{ route('home') }}" class="text-yellow-400 hover:text-yellow-300">
                    <i class="fas fa-home mr-2"></i>Kembali ke Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
