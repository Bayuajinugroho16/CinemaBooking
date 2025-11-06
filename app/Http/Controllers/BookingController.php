<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Studio;
use App\Models\Seat;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function showBookingPage($filmId)
    {
        $film = Film::findOrFail($filmId);
        $studios = Studio::with(['seats' => function($query) {
            $query->orderBy('row')->orderBy('number');
        }])->get();

        $showTimes = [
            '10:00', '13:00', '16:00', '19:00', '22:00'
        ];

        return view('booking.index', compact('film', 'studios', 'showTimes'));
    }

    public function getSeats($studioId)
    {
        $seats = Seat::where('studio_id', $studioId)
                    ->orderBy('row')
                    ->orderBy('number')
                    ->get()
                    ->groupBy('row');

        return response()->json($seats);
    }

    public function bookSeats(Request $request)
    {
        $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'seats' => 'required|array',
            'seats.*' => 'exists:seats,id'
        ]);

        $film = Film::find($request->film_id);
        $selectedSeats = Seat::whereIn('id', $request->seats)->get();

        // Check if seats are available
        foreach ($selectedSeats as $seat) {
            if (!$seat->is_available) {
                return back()->with('error', 'Kursi ' . $seat->seat_code . ' sudah dipesan!');
            }
        }

        // Calculate total price
        $totalPrice = 0;
        foreach ($selectedSeats as $seat) {
            $seatPrice = $film->price;
            if ($seat->type === 'sweetbox') {
                $seatPrice += 20000;
            }
            $totalPrice += $seatPrice;
        }

        // Create booking with pending status
        $booking = Booking::create([
            'user_id' => Auth::id() ?? 1,
            'film_id' => $request->film_id,
            'studio_id' => $request->studio_id,
            'show_date' => $request->show_date,
            'show_time' => $request->show_time,
            'total_seats' => count($request->seats),
            'total_price' => $totalPrice,
            'status' => 'pending', // Status pending sampai pembayaran verified
            'payment_status' => 'pending'
        ]);

        // Attach seats to booking (tapi jangan update availability dulu)
        $booking->seats()->attach($request->seats);

        return redirect()->route('booking.payment', $booking->id)
                        ->with('success', 'Silakan lakukan pembayaran!');
    }

    public function payment($bookingId)
    {
        $booking = Booking::with(['film', 'studio', 'seats'])->findOrFail($bookingId);

        // QRIS code dummy (dalam real app, generate dari payment gateway)
        $qrisCode = "00020101021126680014ID.CO.QRIS.WWW01189360091100012899510213Duitin QRIS52045812530336054061000005802ID5914CINEMA XXI IND6007Jakarta61051234062380114Duitin QRIS6304";

        return view('booking.payment', compact('booking', 'qrisCode'));
    }

    public function uploadPayment(Request $request, $bookingId)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $booking = Booking::findOrFail($bookingId);

        // Upload bukti pembayaran
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $booking->update([
                'payment_proof' => $path,
                'payment_status' => 'pending' // Menunggu verifikasi admin
            ]);
        }

        return redirect()->route('booking.pending', $booking->id)
                        ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    public function pending($bookingId)
    {
        $booking = Booking::with(['film', 'studio', 'seats'])->findOrFail($bookingId);
        return view('booking.pending', compact('booking'));
    }

    // ADMIN FUNCTION - untuk verifikasi pembayaran
    public function verifyPayment($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Update status pembayaran
        $booking->update([
            'payment_status' => 'verified',
            'status' => 'confirmed',
            'paid_at' => now()
        ]);

        // Update seat availability
        $booking->seats()->update(['is_available' => false]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    public function rejectPayment($bookingId, Request $request)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->update([
            'payment_status' => 'rejected',
            'admin_notes' => $request->admin_notes
        ]);

        return back()->with('success', 'Pembayaran ditolak!');
    }
}
