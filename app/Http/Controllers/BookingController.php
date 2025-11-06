<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Studio;
use App\Models\Seat;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // ← TAMBAHKAN INI
use Illuminate\Support\Facades\Log; // ← TAMBAHKAN INI
use Illuminate\Support\Facades\Validator; // ← TAMBAHKAN INI

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

    // Generate dynamic QRIS data berdasarkan booking
    $amount = str_pad($booking->total_price, 13, '0', STR_PAD_LEFT); // Format amount untuk QRIS
    $merchantName = "CINEMA XXI BOOKING";
    $bookingCode = str_pad($booking->id, 10, '0', STR_PAD_LEFT);

    // QRIS format dengan data dinamis
    $qrisCode = "000201" . // Payload Format Indicator
                "010211" . // Point of Initiation Method
                "26680014ID.CO.QRIS.WWW" . // Global Unique Identifier
                "011893600911000128995" . // Merchant Account Information
                "0106" . $bookingCode . // Booking ID
                "0208" . $amount . // Amount
                "52045812" . // Merchant Category Code
                "5303604" . // Currency (IDR)
                "5406" . $amount . // Transaction Amount
                "5802ID" . // Country Code
                "5906" . substr($merchantName, 0, 6) . // Merchant Name
                "6007Jakarta" . // Merchant City
                "610512340" . // Postal Code
                "62380114Duitin QRIS" . // Additional Data Field Template
                "6304"; // CRC

    return view('booking.payment', compact('booking', 'qrisCode'));
}

    public function showPaymentProof($id)
    {
        $booking = Booking::where('user_id', auth()->id())->findOrFail($id);

        if (!$booking->payment_proof) {
            abort(404, 'Bukti pembayaran tidak ditemukan');
        }

        $filePath = storage_path('app/public/payment-proofs/' . $booking->payment_proof);

        if (!file_exists($filePath)) {
            abort(404, 'File bukti pembayaran tidak ditemukan');
        }

        return response()->file($filePath);
    }

public function uploadPaymentProof(Request $request, $id)
{
    try {
        if (!$request->hasFile('payment_proof')) {
            return redirect()->back()->with('error', 'Harap pilih file');
        }

        $file = $request->file('payment_proof');

        $booking = Booking::where('user_id', auth()->id())->find($id);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking tidak ditemukan');
        }

        $filename = 'proof_' . $id . '_' . time() . '.jpg';
        $file->storeAs('payment-proofs', $filename, 'public');

        $booking->payment_proof = $filename;
        $booking->payment_status = 'pending';
        $booking->save();

        // ⬇️ REDIRECT KE MY-TICKET ⬇️
        return redirect()->route('booking.myTicket')
                        ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    // TAMBAHKAN METHOD INI YANG BELUM ADA
    public function showUserBooking($id)
    {
        $booking = Booking::with(['film', 'studio', 'seats'])
                          ->where('user_id', auth()->id())
                          ->findOrFail($id);

        return view('user.booking-detail', compact('booking'));
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

    // TAMBAHKAN METHOD YANG DIPERLUKAN
    public function myTicket()
    {
        $bookings = Booking::with(['film', 'studio', 'seats'])
                          ->where('user_id', auth()->id())
                          ->latest()
                          ->get();

        return view('booking.my-ticket', compact('bookings'));
    }

    public function confirmation($id)
    {
        $booking = Booking::with(['film', 'studio', 'seats'])
                          ->where('user_id', auth()->id())
                          ->findOrFail($id);

        return view('booking.confirmation', compact('booking'));
    }
}
