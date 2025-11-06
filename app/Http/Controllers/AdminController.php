<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Film;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        // Update seat availability
        $booking->seats()->update(['is_available' => false]);

        return redirect()->route('admin.bookings')
            ->with('success', 'Pembayaran berhasil diverifikasi! Kursi sekarang terbooking.');
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
            abort(404, 'Bukti pembayaran tidak ditemukan');
        }

        return response()->file(storage_path('app/public/' . $booking->payment_proof));
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
