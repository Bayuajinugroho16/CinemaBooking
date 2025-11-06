<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Film;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_payments' => Booking::where('payment_status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_users' => User::count(),
            'total_films' => Film::count(),
        ];

        $recent_bookings = Booking::with(['user', 'film', 'seats'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings'));
    }

    public function bookings(Request $request)
    {
        $status = $request->get('status', 'all');
        $payment_status = $request->get('payment_status', 'all');

        $query = Booking::with(['user', 'film', 'studio', 'seats']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($payment_status !== 'all') {
            $query->where('payment_status', $payment_status);
        }

        $bookings = $query->latest()->get();

        return view('admin.bookings', compact('bookings', 'status', 'payment_status'));
    }

    public function showBooking($id)
    {
        $booking = Booking::with(['user', 'film', 'studio', 'seats'])->findOrFail($id);
        return view('admin.booking-detail', compact('booking'));
    }

    public function verifyPayment($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'payment_status' => 'verified',
            'status' => 'confirmed',
            'paid_at' => now()
        ]);

        $booking->seats()->update(['is_available' => false]);

        return redirect()->route('admin.bookings')
            ->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        $booking = Booking::findOrFail($id);

        $booking->update([
            'payment_status' => 'rejected',
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->route('admin.bookings')
            ->with('success', 'Pembayaran ditolak!');
    }

public function viewPaymentProof($id)
{
    $booking = Booking::findOrFail($id);

    if (!$booking->payment_proof) {
        abort(404, 'Bukti pembayaran tidak ditemukan di database');
    }

    // Debug: Log informasi
    \Log::info('Admin viewing payment proof:', [
        'booking_id' => $booking->id,
        'filename' => $booking->payment_proof,
        'user_id' => $booking->user_id
    ]);

    // Coba berbagai path yang mungkin
    $possiblePaths = [
        storage_path('app/public/payment-proofs/' . $booking->payment_proof),
        storage_path('app/public/payment-proofs/' . $booking->payment_proof),
        public_path('storage/payment-proofs/' . $booking->payment_proof),
        storage_path('app/public/' . $booking->payment_proof),
        public_path('storage/' . $booking->payment_proof),
    ];

    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            \Log::info('File found at: ' . $path);
            return response()->file($path);
        }
        \Log::info('File not found at: ' . $path);
    }

    // Jika tidak ditemukan, beri info detail
    abort(404, "File bukti pembayaran tidak ditemukan.
           \nFilename: " . $booking->payment_proof . "
           \nCek folder: storage/app/public/payment-proofs/");
}

public function destroyBooking($id)
{
    try {
        $booking = Booking::findOrFail($id);

        \Log::info('Deleting booking:', [
            'booking_id' => $booking->id,
            'user' => $booking->user->name,
            'film' => $booking->film->title
        ]);

        // Hapus file bukti pembayaran jika ada
        if ($booking->payment_proof) {
            $filePath = storage_path('app/public/payment-proofs/' . $booking->payment_proof);
            if (file_exists($filePath)) {
                unlink($filePath);
                \Log::info('Deleted payment proof file: ' . $booking->payment_proof);
            }
        }

        // Hapus relasi seats terlebih dahulu
        $booking->seats()->detach();

        // Hapus booking
        $booking->delete();

        return redirect()->route('admin.bookings')
            ->with('success', 'Booking berhasil dihapus!');

    } catch (\Exception $e) {
        \Log::error('Error deleting booking: ' . $e->getMessage());

        return redirect()->route('admin.bookings')
            ->with('error', 'Gagal menghapus booking: ' . $e->getMessage());
    }
}

    public function films()
    {
        $films = Film::withCount('bookings')->latest()->get();
        return view('admin.films', compact('films'));
    }

    public function users()
    {
        $users = User::withCount('bookings')->latest()->get();
        return view('admin.users', compact('users'));
    }
}
