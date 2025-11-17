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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Film & Studio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Show Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $booking->id }}</td>
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
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-800' :
                                       ($booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $booking->payment_status == 'verified' ? 'bg-green-100 text-green-800' :
                                       ($booking->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
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
                                    <!-- Verify Button dengan Popup -->
                                    <button onclick="showVerifyModal({{ $booking->id }})"
                                            class="text-green-600 hover:text-green-900">Verify</button>

                                    <!-- Reject Button dengan Popup -->
                                    <button onclick="showRejectModal({{ $booking->id }})"
                                        class="text-red-600 hover:text-red-900">Reject</button>
                                @endif

                                <!-- Delete Button dengan Popup -->
                                <button onclick="showDeleteModal({{ $booking->id }})"
                                        class="text-red-600 hover:text-red-900" title="Delete Booking">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Verify Confirmation Modal -->
    <div id="verifyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Verifikasi Pembayaran?</h3>
                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-500 text-center">
                        Apakah Anda yakin ingin memverifikasi pembayaran ini?
                    </p>
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        Booking ID: <span id="verifyBookingId" class="font-semibold"></span>
                    </p>
                </div>
                <div class="mt-4 flex justify-center space-x-3">
                    <button type="button" onclick="hideVerifyModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <form id="verifyForm" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-check mr-1"></i> Ya, Verifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Payment Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Tolak Pembayaran?</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 text-center">Alasan penolakan:</label>
                        <textarea name="admin_notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                            required placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                    <div class="mt-4 flex justify-center space-x-3">
                        <button type="button" onclick="hideRejectModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            <i class="fas fa-times mr-1"></i> Ya, Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Hapus Booking?</h3>
                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-500 text-center">
                        Apakah Anda yakin ingin menghapus booking ini?
                    </p>
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        Booking ID: <span id="deleteBookingId" class="font-semibold"></span>
                    </p>
                </div>
                <div class="mt-4 flex justify-center space-x-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            <i class="fas fa-trash mr-1"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Verify Modal Functions
        function showVerifyModal(bookingId) {
            document.getElementById('verifyBookingId').textContent = '#' + bookingId;
            document.getElementById('verifyForm').action = `/admin/bookings/${bookingId}/verify`;
            document.getElementById('verifyModal').classList.remove('hidden');
        }

        function hideVerifyModal() {
            document.getElementById('verifyModal').classList.add('hidden');
        }

        // Reject Modal Functions
        function showRejectModal(bookingId) {
            document.getElementById('rejectForm').action = `/admin/bookings/${bookingId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Delete Modal Functions
        function showDeleteModal(bookingId) {
            document.getElementById('deleteBookingId').textContent = '#' + bookingId;
            document.getElementById('deleteForm').action = `/admin/bookings/${bookingId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.id === 'verifyModal') hideVerifyModal();
            if (event.target.id === 'rejectModal') hideRejectModal();
            if (event.target.id === 'deleteModal') hideDeleteModal();
        });
    </script>
@endsection
