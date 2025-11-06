<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Cinema Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }
        .cinema-glow {
            box-shadow: 0 0 50px rgba(255, 193, 7, 0.1);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"none\" fill-rule=\"evenodd\"><g fill=\"%23ffffff\" fill-opacity=\"0.1\"><circle cx=\"30\" cy=\"30\" r=\"2\"/></g></svg>')"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Header Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-yellow-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-film text-black text-2xl"></i>
                </div>
                <div class="text-left">
                    <h1 class="text-3xl font-black text-white">
                        <span class="text-yellow-400">CINEMA</span>
                        <span class="text-white">BOOKING</span>
                    </h1>
                    <p class="text-gray-400 text-sm">Buat Akun Baru</p>
                </div>
            </div>
        </div>

        <!-- Register Card -->
        <div class="bg-gray-800 rounded-3xl shadow-2xl cinema-glow border border-gray-700 p-8">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name Input -->
                <div class="mb-6">
                    <label for="name" class="block text-gray-300 text-sm font-semibold mb-3">
                        <i class="fas fa-user mr-2 text-yellow-400"></i>Nama Lengkap
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-id-card text-gray-500"></i>
                        </div>
                        <input id="name"
                               name="name"
                               type="text"
                               value="{{ old('name') }}"
                               required
                               autofocus
                               autocomplete="name"
                               class="w-full bg-gray-700 border border-gray-600 rounded-2xl py-4 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200"
                               placeholder="masukkan nama lengkap">
                    </div>
                    @error('name')
                        <p class="text-red-400 text-sm mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email Input -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-300 text-sm font-semibold mb-3">
                        <i class="fas fa-envelope mr-2 text-yellow-400"></i>Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-at text-gray-500"></i>
                        </div>
                        <input id="email"
                               name="email"
                               type="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="username"
                               class="w-full bg-gray-700 border border-gray-600 rounded-2xl py-4 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200"
                               placeholder="masukkan email anda">
                    </div>
                    @error('email')
                        <p class="text-red-400 text-sm mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-300 text-sm font-semibold mb-3">
                        <i class="fas fa-lock mr-2 text-yellow-400"></i>Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-500"></i>
                        </div>
                        <input id="password"
                               name="password"
                               type="password"
                               required
                               autocomplete="new-password"
                               class="w-full bg-gray-700 border border-gray-600 rounded-2xl py-4 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200"
                               placeholder="buat password">
                    </div>
                    @error('password')
                        <p class="text-red-400 text-sm mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password Input -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-300 text-sm font-semibold mb-3">
                        <i class="fas fa-lock mr-2 text-yellow-400"></i>Konfirmasi Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-shield-alt text-gray-500"></i>
                        </div>
                        <input id="password_confirmation"
                               name="password_confirmation"
                               type="password"
                               required
                               autocomplete="new-password"
                               class="w-full bg-gray-700 border border-gray-600 rounded-2xl py-4 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200"
                               placeholder="ulangi password">
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-black font-bold py-4 rounded-2xl shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center group">
                    <i class="fas fa-user-plus mr-3 group-hover:rotate-12 transition-transform"></i>
                    BUAT AKUN BARU
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 pt-6 border-t border-gray-700 text-center">
                <p class="text-gray-400">
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                       class="text-yellow-400 hover:text-yellow-300 font-semibold transition duration-200 ml-1">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-gray-500 text-sm">
                &copy; {{ date('Y') }} Cinema Booking System
            </p>
        </div>
    </div>

    <script>
        // Interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-yellow-500');
                });
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-yellow-500');
                });
            });
        });
    </script>
</body>
</html> 
