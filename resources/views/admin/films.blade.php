@extends('layouts.admin')

@section('title', 'Manage Films')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manage Films</h1>
        <p class="text-gray-600">Total: {{ $films->count() }} films</p>
    </div>
    <a href="{{ route('admin.films.create') }}"
       class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 flex items-center">
        <i class="fas fa-plus mr-2"></i>Add New Film
    </a>
</div>

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

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Film</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Genre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($films as $film)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($film->image)
                            <div class="flex-shrink-0 h-10 w-10 mr-3">
                                <img class="h-10 w-10 rounded-lg object-cover"
                                     src="{{ asset('storage/' . $film->image) }}"
                                     alt="{{ $film->title }}">
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $film->title }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($film->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $film->genre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $film->duration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Rp {{ number_format($film->price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $film->status == 'playing' ? 'bg-green-100 text-green-800' :
                               ($film->status == 'upcoming' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($film->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-blue-700 bg-blue-100 rounded-full">
                            {{ $film->bookings_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <!-- Edit Button -->
                        <a href="{{ route('admin.films.edit', $film->id) }}"
                           class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.films.destroy', $film->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-900 inline-flex items-center"
                                    onclick="return confirm('Are you sure you want to delete {{ $film->title }}?')">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Empty State -->
@if($films->count() == 0)
<div class="text-center py-12 bg-white rounded-lg shadow mt-4">
    <i class="fas fa-film text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-500 text-lg mb-2">No films found.</p>
    <a href="{{ route('admin.films.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
        Add your first film
    </a>
</div>
@endif
@endsection
