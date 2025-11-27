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

    public function getSeats($studioId, Request $request)
{
    $filmId = $request->get('film_id');
    $showDate = $request->get('show_date');
    $showTime = $request->get('show_time');

    Log::info('🎯 GET SEATS REQUEST:', [
        'studio_id' => $studioId,
        'film_id' => $filmId,
        'show_date' => $showDate,
        'show_time' => $showTime
    ]);

    $seats = Seat::where('studio_id', $studioId)
                ->orderBy('row')
                ->orderBy('number')
                ->get();

    // ⬇️ Tandai seats yang sudah dibooking untuk film dan showtime ini
    if ($filmId && $showDate && $showTime) {
        // Ambil seat_ids yang sudah dibooking (verified) untuk film + showtime ini
        $bookedSeatIds = DB::table('bookings')
            ->join('booking_seat', 'bookings.id', '=', 'booking_seat.booking_id')
            ->where('bookings.film_id', $filmId)
            ->where('bookings.studio_id', $studioId)
            ->where('bookings.show_date', $showDate)
            ->where('bookings.show_time', $showTime)
            ->where('bookings.payment_status', 'verified')
            ->pluck('booking_seat.seat_id')
            ->toArray();

        Log::info('🔍 BOOKED SEATS FOUND:', [
            'film_id' => $filmId,
            'booked_seat_ids' => $bookedSeatIds
        ]);

        // Tambah status is_booked ke response
        $seats->each(function($seat) use ($bookedSeatIds) {
            $seat->is_booked = in_array($seat->id, $bookedSeatIds);
            $seat->is_available = !in_array($seat->id, $bookedSeatIds); // Update is_available juga
        });
    }

    Log::info('🎯 FINAL SEATS DATA:', [
        'total_seats' => $seats->count(),
        'booked_seats' => $seats->where('is_booked', true)->pluck('seat_code')
    ]);

    return response()->json($seats->groupBy('row'));
}

public function bookSeats(Request $request)
{
    try {
        DB::beginTransaction();

        Log::info('🎫 === BOOKING ATTEMPT START ===', $request->all());

        $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'seats' => 'required|array|min:1',
            'seats.*' => 'exists:seats,id'
        ]);

        $film = Film::findOrFail($request->film_id);
        $selectedSeats = Seat::whereIn('id', $request->seats)->get();

        Log::info('🎫 SELECTED SEATS DETAIL:', [
            'film' => $film->title,
            'film_id' => $request->film_id,
            'studio_id' => $request->studio_id,
            'show_date' => $request->show_date,
            'show_time' => $request->show_time,
            'selected_seats' => $selectedSeats->pluck('seat_code'),
            'selected_seat_ids' => $selectedSeats->pluck('id')
        ]);

        // ⬇️ VALIDASI DETAIL - CEK SETIAP KURSI
        $unavailableSeats = [];

        foreach ($selectedSeats as $seat) {
            Log::info('🔍 CHECKING SEAT:', [
                'seat_id' => $seat->id,
                'seat_code' => $seat->seat_code
            ]);

            // Cek apakah ada booking VERIFIED untuk seat ini di film + showtime yang sama
            $existingBooking = DB::table('bookings')
                ->join('booking_seat', 'bookings.id', '=', 'booking_seat.booking_id')
                ->where('booking_seat.seat_id', $seat->id)
                ->where('bookings.film_id', $request->film_id)
                ->where('bookings.studio_id', $request->studio_id)
                ->where('bookings.show_date', $request->show_date)
                ->where('bookings.show_time', $request->show_time)
                ->where('bookings.payment_status', 'verified')
                ->exists();

            Log::info('🔍 SEAT CHECK RESULT:', [
                'seat_code' => $seat->seat_code,
                'already_booked' => $existingBooking
            ]);

            if ($existingBooking) {
                $unavailableSeats[] = $seat->seat_code;
                Log::warning('🚫 SEAT ALREADY BOOKED:', [
                    'seat' => $seat->seat_code,
                    'film' => $film->title
                ]);
            }
        }

        if (!empty($unavailableSeats)) {
            DB::rollBack();
            Log::error('❌ BOOKING BLOCKED - Seats already booked:', $unavailableSeats);
            return back()
                ->with('error', 'Kursi ' . implode(', ', $unavailableSeats) . ' sudah dipesan untuk film dan jam tayang ini!')
                ->withInput();
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'film_id' => $request->film_id,
            'studio_id' => $request->studio_id,
            'show_date' => $request->show_date,
            'show_time' => $request->show_time,
            'total_seats' => count($request->seats),
            'total_price' => $film->price * count($request->seats),
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        // Attach seats to booking
        $booking->seats()->attach($request->seats);

        Log::info('🟢 BOOKING CREATED SUCCESSFULLY:', [
            'booking_id' => $booking->id,
            'film' => $film->title,
            'seats' => $selectedSeats->pluck('seat_code')
        ]);

        DB::commit();

        return redirect()->route('booking.payment', $booking->id)
                        ->with('success', 'Booking berhasil! Silakan upload bukti pembayaran.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('🔴 BOOKING ERROR: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
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

    // Tambahkan method ini di BookingController
public function debugSeats($bookingId)
{
    try {
        $booking = Booking::with(['seats', 'film', 'studio'])->findOrFail($bookingId);

        $result = [
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'paid_at' => $booking->paid_at,
                'film' => $booking->film->title,
                'studio' => $booking->studio->name
            ],
            'seats' => []
        ];

        foreach ($booking->seats as $seat) {
            $result['seats'][] = [
                'id' => $seat->id,
                'code' => $seat->seat_code,
                'available' => (bool)$seat->is_available,
                'row' => $seat->row,
                'number' => $seat->number
            ];
        }

        return response()->json($result);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
