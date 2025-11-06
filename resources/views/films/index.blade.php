<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINEMA XXI - Bioskop Terbaik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <header
        class="flex items-center justify-between px-6 py-4 bg-black shadow-2xl sticky top-0 z-50 border-b border-yellow-500">
        <!-- Logo -->
        <div class="text-2xl font-bold text-white tracking-wider">
            <span class="text-yellow-400 font-black">CINEMA</span>
            <span class="text-white font-normal">BOOKING</span>
        </div>

        <!-- Navigasi -->
        <nav class="flex items-center space-x-6">
            <a href="#"
                class="flex items-center text-yellow-400 font-semibold text-sm border-b-2 border-yellow-400 pb-1">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <a href="#" class="flex items-center text-white hover:text-yellow-400 transition text-sm">
                <i class="fas fa-ticket-alt mr-2"></i> Pesanan Saya
            </a>
            {{-- <!-- Tombol login/member -->
            <button
                class="bg-yellow-500 text-black px-4 py-2 rounded-lg font-bold text-sm hover:bg-yellow-400 transition">
                <i class="fas fa-crown mr-2"></i> LOGIN MEMBER
            </button> --}}
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

    <!-- Search Bar -->
    <div class="px-6 py-8 flex justify-center bg-gray-900">
        <div class="relative w-full md:w-3/4 lg:w-2/3">
            <input type="text" placeholder="Cari film, sutradara, atau genre..."
                class="w-full p-4 pl-12 rounded-full bg-gray-800 text-white placeholder-gray-400
                          border border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500
                          focus:border-yellow-500 transition duration-300 shadow-lg" />
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Promo Banner -->
    <section class="px-6 py-4 bg-gray-900">
        <div
            class="max-w-7xl mx-auto bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl p-6 text-center shadow-2xl">
            <h2 class="text-2xl font-black text-black mb-2">CINEMA BOOKING</h2>
            <p class="text-gray-900 font-semibold">Dibuat Oleh Kelompok 7 Pemrogaraman Web</p>
            <button class="mt-4 bg-black text-yellow-400 px-6 py-2 rounded-full font-bold hover:bg-gray-800 transition">
                Lihat Film
            </button>
        </div>
    </section>

    <!-- Carousel: Film Sedang Tayang -->
    <section class="px-6 py-8 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-black mb-6 flex items-center text-white">
                <i class="fas fa-film text-yellow-500 mr-3"></i> SEDANG TAYANG
            </h2>

            <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                @forelse($films as $film)
                    <!-- Kartu Film -->
                    <div
                        class="relative flex-shrink-0 w-64 bg-gray-800 rounded-xl shadow-xl border border-gray-700
                            hover:shadow-yellow-500/20 transform transition duration-300 ease-in-out hover:scale-[1.02]">

                        <!-- Badge Harga -->
                        <span
                            class="absolute top-3 left-3 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-lg">
                            Rp {{ number_format($film->price, 0, ',', '.') }}
                        </span>

                        <!-- Poster Film dari Storage Public -->
                        @if ($film->image)
                            <img src="{{ asset('storage/' . $film->image) }}" alt="{{ $film->title }}"
                                class="h-80 w-full object-cover rounded-t-xl opacity-90 transition duration-200 hover:opacity-100">
                        @else
                            <div class="h-80 w-full bg-gray-700 rounded-t-xl flex items-center justify-center">
                                <i class="fas fa-film text-gray-500 text-4xl"></i>
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="text-lg font-bold text-white truncate">{{ $film->title }}</h3>
                            <p class="text-yellow-400 text-sm mt-1 font-medium">
                                {{ $film->genre ?? 'Genre tidak tersedia' }}</p>

                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center text-yellow-400">
                                    <i class="fas fa-clock text-sm"></i>
                                    <span class="ml-1 text-sm font-semibold">{{ $film->duration ?? 'N/A' }}</span>
                                </div>
                                <span class="text-gray-400 text-sm">Rp
                                    {{ number_format($film->price, 0, ',', '.') }}</span>
                            </div>

                            <p class="text-gray-400 text-xs mt-2 line-clamp-2">{{ Str::limit($film->description, 80) }}
                            </p>

                            <a href="{{ route('films.book', $film->id) }}"
                                class="mt-4 w-full block text-center bg-yellow-500 text-gray-900 px-4 py-3 text-sm font-bold rounded-lg
          hover:bg-yellow-400 transition duration-150 shadow-md hover:shadow-lg">
                                <i class="fas fa-shopping-cart mr-2"></i> BELI TIKET
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="flex-shrink-0 w-64 bg-gray-800 rounded-xl p-8 text-center">
                        <i class="fas fa-film text-gray-500 text-4xl mb-4"></i>
                        <p class="text-gray-400">Tidak ada film yang sedang tayang</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Pilihan Film Lainnya -->
    <section class="px-6 py-8 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-black mb-6 flex items-center text-white">
                <i class="fas fa-ticket-alt text-yellow-500 mr-3"></i> PILIHAN FILM LAINNYA
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($films_other as $film)
                    <!-- Kartu Film Kecil -->
                    <div class="bg-gray-800 rounded-lg shadow-md border border-gray-700
                            hover:shadow-gray-700/50 transform transition duration-200 hover:scale-105 cursor-pointer"
                        onclick="window.location='{{ route('films.show', $film->id) }}'">

                        <!-- Poster Film dari Storage Public -->
                        @if ($film->image)
                            <img src="{{ asset('storage/' . $film->image) }}" alt="{{ $film->title }}"
                                class="h-48 w-full object-cover rounded-t-lg">
                        @else
                            <div class="h-48 w-full bg-gray-700 rounded-t-lg flex items-center justify-center">
                                <i class="fas fa-film text-gray-500 text-2xl"></i>
                            </div>
                        @endif

                        <div class="p-3">
                            <h3 class="text-gray-100 text-sm font-semibold text-center truncate">
                                {{ $film->title }}
                            </h3>
                            <div class="flex items-center justify-center mt-1">
                                <span
                                    class="text-yellow-400 text-xs font-medium">{{ $film->genre ?? 'General' }}</span>
                            </div>
                            <div class="flex items-center justify-center mt-2">
                                <span class="text-green-400 text-xs font-bold">
                                    Rp {{ number_format($film->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-6 text-center py-8">
                        <i class="fas fa-film text-gray-500 text-4xl mb-4"></i>
                        <p class="text-gray-400">Tidak ada pilihan film lainnya</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Film Akan Datang -->
    <section class="px-6 py-8 bg-gray-900">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-black mb-6 flex items-center text-white">
                <i class="fas fa-clock text-yellow-500 mr-3"></i> SEGERA TAYANG
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($upcoming_films as $film)
                    <div
                        class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-yellow-500 transition duration-300">
                        <div class="flex items-start space-x-4">
                            @if ($film->image)
                                <img src="{{ asset('storage/' . $film->image) }}" alt="{{ $film->title }}"
                                    class="w-24 h-32 object-cover rounded-lg">
                            @else
                                <div class="w-24 h-32 bg-gray-700 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-film text-gray-500"></i>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-white">{{ $film->title }}</h3>
                                <p class="text-yellow-400 text-sm mt-1">{{ $film->genre ?? 'Genre tidak tersedia' }}
                                </p>
                                <p class="text-gray-400 text-xs mt-2 line-clamp-2">
                                    {{ Str::limit($film->description, 60) }}</p>

                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-yellow-400 text-sm">
                                        <i class="fas fa-clock"></i> {{ $film->duration ?? 'N/A' }}
                                    </span>
                                    <span class="text-green-400 text-sm font-bold">Rp
                                        {{ number_format($film->price, 0, ',', '.') }}</span>
                                </div>

                                <button
                                    class="mt-3 bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-600 transition">
                                    <i class="fas fa-bell mr-2"></i> REMIND ME
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8">
                        <i class="fas fa-clock text-gray-500 text-4xl mb-4"></i>
                        <p class="text-gray-400">Tidak ada film yang akan datang</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

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
                        <a href="#" class="text-gray-400 hover:text-yellow-400 transition"><i
                                class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-yellow-400 transition"><i
                                class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-yellow-400 transition"><i
                                class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6">
                <p>&copy; {{ date('Y') }} Cinema XXI. All rights reserved.</p>
                <p class="text-xs mt-2">Didesain dengan <span class="text-red-500">❤</span> menggunakan Tailwind CSS
                </p>
            </div>
        </div>
    </footer>

</body>

</html>
