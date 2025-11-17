@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
    <div class="min-h-screen bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center">
                        <i class="fas fa-users text-yellow-500 mr-3"></i>
                        Manage Users
                    </h1>
                    <p class="text-gray-400 mt-2">Total: {{ $users->count() }} users</p>
                </div>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="bg-green-900/30 border border-green-700 text-green-300 p-4 rounded-xl mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-900/30 border border-red-700 text-red-300 p-4 rounded-xl mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- Users Table -->
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-2"></i>User
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-envelope mr-2"></i>Email
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user-tag mr-2"></i>Role
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-ticket-alt mr-2"></i>Total Bookings
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-calendar-plus mr-2"></i>Registered
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-cogs mr-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-750 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-yellow-500 rounded-full flex items-center justify-center">
                                                <span class="text-gray-900 font-bold text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-400">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 text-xs rounded-full font-semibold
                                    {{ $user->role == 'admin'
                                        ? 'bg-purple-900 text-purple-300'
                                        : ($user->role == 'staff'
                                            ? 'bg-blue-900 text-blue-300'
                                            : 'bg-green-900 text-green-300') }}">
                                            <i
                                                class="fas {{ $user->role == 'admin' ? 'fa-crown' : ($user->role == 'staff' ? 'fa-user-shield' : 'fa-user') }} mr-1"></i>
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-300 flex items-center">
                                            <i class="fas fa-ticket-alt text-yellow-500 mr-2"></i>
                                            {{ $user->bookings_count }} bookings
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        <i class="fas fa-clock mr-2"></i>
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <!-- View Profile Button -->
                                            <a href="#" class="text-blue-400 hover:text-blue-300 transition"
                                                title="View Profile">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="#" class="text-green-400 hover:text-green-300 transition"
                                                title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Delete Button -->
                                            <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                                class="text-red-400 hover:text-red-300 transition" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <!-- Delete Form (Hidden) -->
                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            @if ($users->count() == 0)
                <div class="bg-gray-800 rounded-xl shadow-lg p-8 text-center border border-gray-700">
                    <i class="fas fa-users text-gray-500 text-6xl mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-300 mb-2">No Users Found</h2>
                    <p class="text-gray-500 mb-6">There are no users registered in the system yet.</p>
                </div>
            @endif

            <!-- Statistics -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-900/30 rounded-lg">
                            <i class="fas fa-users text-blue-400 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Total Users</p>
                            <p class="text-2xl font-bold text-white">{{ $users->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-900/30 rounded-lg">
                            <i class="fas fa-user text-green-400 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Regular Users</p>
                            <p class="text-2xl font-bold text-white">{{ $users->where('role', 'user')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-900/30 rounded-lg">
                            <i class="fas fa-crown text-purple-400 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Admins</p>
                            <p class="text-2xl font-bold text-white">{{ $users->where('role', 'admin')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 border border-gray-700">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-red-900/30 rounded-lg mr-3">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Confirm Delete</h3>
            </div>

            <p class="text-gray-300 mb-6" id="deleteMessage">
                Are you sure you want to delete this user?
            </p>

            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-gray-300 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </button>
                <button id="confirmDeleteBtn"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 transition flex items-center">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        let userIdToDelete = null;

        function confirmDelete(userId, userName) {
            userIdToDelete = userId;
            const deleteMessage = document.getElementById('deleteMessage');
            deleteMessage.textContent = `Are you sure you want to delete user "${userName}"? This action cannot be undone.`;

            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            userIdToDelete = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (userIdToDelete) {
                document.getElementById('delete-form-' + userIdToDelete).submit();
            }
        });

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>

    <style>
        .bg-gray-750 {
            background-color: #374151;
        }
    </style>
@endsection
