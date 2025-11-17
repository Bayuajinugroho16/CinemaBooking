<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Film - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Add New Film</h1>
                <a href="{{ route('admin.films') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Films
                </a>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Info:</strong> Film yang ditambahkan akan otomatis masuk ke <strong class="text-green-600">SEDANG TAYANG</strong> dan bisa langsung dipesan oleh user.
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Film Title *</label>
                                <input type="text" name="title" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('title') }}"
                                       placeholder="Enter film title">
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Genre -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Genre *</label>
                                <input type="text" name="genre" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('genre') }}"
                                       placeholder="e.g., Action, Drama, Comedy">
                                @error('genre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Duration *</label>
                                <input type="text" name="duration" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('duration') }}"
                                       placeholder="e.g., 2h 15m">
                                @error('duration') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                                <input type="number" name="price" required min="0" step="1000"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('price') }}"
                                       placeholder="Enter ticket price">
                                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Poster Image *</label>
                        <input type="file" name="image" accept="image/*" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               onchange="previewImage(this)">
                        @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-2 hidden">
                            <img id="preview" class="h-32 w-auto border rounded-lg shadow-sm">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea name="description" required rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter film description">{{ old('description') }}</textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit"
                                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                            <i class="fas fa-save mr-2"></i>Add Film to SEDANG TAYANG
                        </button>
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
