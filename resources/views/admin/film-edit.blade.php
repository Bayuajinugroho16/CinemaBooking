<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Film - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Cinema Admin</a>
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-200 {{ request()->routeIs('admin.dashboard') ? 'text-blue-300' : '' }}">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.bookings') }}" class="hover:text-blue-200 {{ request()->routeIs('admin.bookings') ? 'text-blue-300' : '' }}">
                            <i class="fas fa-ticket-alt mr-1"></i>Bookings
                        </a>
                        <a href="{{ route('admin.films') }}" class="hover:text-blue-200 {{ request()->routeIs('admin.films*') ? 'text-blue-300' : '' }}">
                            <i class="fas fa-film mr-1"></i>Films
                        </a>
                        <a href="{{ route('admin.users') }}" class="hover:text-blue-200 {{ request()->routeIs('admin.users') ? 'text-blue-300' : '' }}">
                            <i class="fas fa-users mr-1"></i>Users
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Welcome, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-blue-700 hover:bg-blue-600 px-3 py-1 rounded text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Edit Film: {{ $film->title }}</h1>
                <p class="text-gray-600">Update film information</p>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('admin.films.update', $film->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Film Title *</label>
                                <input type="text" name="title" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('title', $film->title) }}"
                                       placeholder="Enter film title">
                            </div>

                            <!-- Genre -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Genre *</label>
                                <input type="text" name="genre" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('genre', $film->genre) }}"
                                       placeholder="e.g., Action, Drama, Comedy">
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Duration *</label>
                                <input type="text" name="duration" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('duration', $film->duration) }}"
                                       placeholder="e.g., 2h 15m">
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                                <input type="number" name="price" required min="0" step="1000"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('price', $film->price) }}"
                                       placeholder="Enter ticket price">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select name="status" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="playing" {{ old('status', $film->status) == 'playing' ? 'selected' : '' }}>Now Playing</option>
                                    <option value="upcoming" {{ old('status', $film->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="other" {{ old('status', $film->status) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Poster Image</label>
                                <input type="file" name="image" accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       onchange="previewImage(this)">

                                <!-- Current Image -->
                                @if($film->image)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 mb-1">Current Image:</p>
                                    <img src="{{ asset('storage/' . $film->image) }}"
                                         alt="{{ $film->title }}"
                                         class="h-32 w-auto border rounded-lg shadow-sm">
                                </div>
                                @endif

                                <!-- New Image Preview -->
                                <div id="imagePreview" class="mt-2 hidden">
                                    <p class="text-sm text-gray-600 mb-1">New Image Preview:</p>
                                    <img id="preview" class="h-32 w-auto border rounded-lg shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea name="description" required rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter film description">{{ old('description', $film->description) }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex space-x-4">
                        <button type="submit"
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Film
                        </button>
                        <a href="{{ route('admin.films') }}"
                           class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('imagePreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
