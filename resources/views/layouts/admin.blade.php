<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Cinema XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="flex h-screen">
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold">
                    <span class="text-yellow-400">CINEMA</span>XXI
                </h1>
                <p class="text-gray-400 text-sm">Admin Panel</p>
            </div>

            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}"
                   class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 border-r-4 border-yellow-400' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                </a>
                <a href="{{ route('admin.bookings') }}"
                   class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.bookings') ? 'bg-gray-700 border-r-4 border-yellow-400' : '' }}">
                    <i class="fas fa-ticket-alt mr-3"></i>Manage Bookings
                </a>
                <a href="{{ route('admin.films') }}"
                   class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.films') ? 'bg-gray-700 border-r-4 border-yellow-400' : '' }}">
                    <i class="fas fa-film mr-3"></i>Films
                </a>
                <a href="{{ route('admin.users') }}"
                   class="block py-3 px-4 hover:bg-gray-700 {{ request()->routeIs('admin.users') ? 'bg-gray-700 border-r-4 border-yellow-400' : '' }}">
                    <i class="fas fa-users mr-3"></i>Users
                </a>
                <a href="{{ route('home') }}"
                   class="block py-3 px-4 hover:bg-gray-700 mt-4 border-t border-gray-700">
                    <i class="fas fa-arrow-left mr-3"></i>Back to Site
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex justify-between items-center px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Admin Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
