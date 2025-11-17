<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CINEMA BOOKING')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
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
               class="flex items-center text-white hover:text-yellow-400 transition text-sm">
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

    <main>
        @yield('content')
    </main>

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

    @stack('scripts')
</body>
</html>
