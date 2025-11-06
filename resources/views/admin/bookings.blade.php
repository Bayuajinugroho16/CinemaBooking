@extends('layouts.admin')

@section('title', 'Manage Bookings')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Bookings</h1>

        <!-- Filter Section -->
        <div class="mt-4 flex space-x-4">
            <a href="{{ route('admin.bookings', ['status' => 'all', 'payment_status' => 'all']) }}"
                class="px-4 py-2 rounded-lg {{ $status == 'all' && $payment_status == 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                All Bookings
            </a>
            <a href="{{ route('admin.bookings', ['status' => 'all', 'payment_status' => 'pending']) }}"
                class="px-4 py-2 rounded-lg {{ $payment_status == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Pending Payment
            </a>
            <a href="{{ route('admin.bookings', ['status' => 'confirmed', 'payment_status' => 'all']) }}"
                class="px-4 py-2 rounded-lg {{ $status == 'confirmed' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Confirmed
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking
                            ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Film &
                            Studio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Show Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $booking->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $booking->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->film->title }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->studio->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->show_date)->format('M d, Y') }}<br>
                                {{ $booking->show_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $booking->seats->pluck('seat_code')->implode(', ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    {{ $booking->status == 'confirmed'
                        ? 'bg-green-100 text-green-800'
                        : ($booking->status == 'pending'
                            ? 'bg-yellow-100 text-yellow-800'
                            : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    {{ $booking->payment_status == 'verified'
                        ? 'bg-green-100 text-green-800'
                        : ($booking->payment_status == 'pending'
                            ? 'bg-yellow-100 text-yellow-800'
                            : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <!-- View Details -->
                                <a href="{{ route('admin.booking.show', $booking->id) }}"
                                    class="text-blue-600 hover:text-blue-900">View</a>

                                <!-- View Payment Proof -->
                                @if ($booking->payment_proof)
                                    <a href="{{ route('admin.booking.payment-proof', $booking->id) }}" target="_blank"
                                        class="text-green-600 hover:text-green-900 font-bold">
                                        🔍 Bukti
                                    </a>
                                @endif

                                <!-- Verify/Reject Buttons -->
                                @if ($booking->payment_status == 'pending')
                                    <form action="{{ route('admin.booking.verify', $booking->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Verify</button>
                                    </form>
                                    <button onclick="showRejectForm({{ $booking->id }})"
                                        class="text-red-600 hover:text-red-900">Reject</button>
                                @endif
                                <!-- Delete Button -->
                                <form action="{{ route('admin.booking.destroy', $booking->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete Booking"
                                        onclick="return confirm('Hapus booking #{{ $booking->id }}? Tindakan ini tidak dapat dibatalkan!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reject Payment Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900">Reject Payment</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Reason for rejection:</label>
                        <textarea name="admin_notes" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                            required></textarea>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="hideRejectForm()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">Reject Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus Booking?</h3>
                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus booking ini?
                        Tindakan ini tidak dapat dibatalkan dan semua data booking akan dihapus permanen.
                    </p>
                </div>
                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk modal reject (yang sudah ada)
        function showRejectForm(bookingId) {
            const form = document.getElementById('rejectForm');
            form.action = `/admin/bookings/${bookingId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function hideRejectForm() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Fungsi untuk modal delete (baru)
        function showDeleteModal(bookingId) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/bookings/${bookingId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Alternative: Confirm sebelum delete (simple)
        function confirmDelete(bookingId) {
            if (confirm(`Hapus booking #${bookingId}? Tindakan ini tidak dapat dibatalkan!`)) {
                document.getElementById(`deleteForm-${bookingId}`).submit();
            }
        }
    </script>

    <script>
        function showRejectForm(bookingId) {
            const form = document.getElementById('rejectForm');
            form.action = `/admin/bookings/${bookingId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function hideRejectForm() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection
