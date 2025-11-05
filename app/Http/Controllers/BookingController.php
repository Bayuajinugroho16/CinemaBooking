<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Studio;
use App\Models\Seat;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                $seatPrice += 20000; // Extra charge for sweetbox
            }
            $totalPrice += $seatPrice;
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'film_id' => $request->film_id,
            'studio_id' => $request->studio_id,
            'show_date' => $request->show_date,
            'show_time' => $request->show_time,
            'total_seats' => count($request->seats),
            'total_price' => $totalPrice,
            'status' => 'confirmed'
        ]);

        // Attach seats to booking
        $booking->seats()->attach($request->seats);

        // Update seat availability
        Seat::whereIn('id', $request->seats)->update(['is_available' => false]);

        return redirect()->route('booking.confirmation', $booking->id)
                        ->with('success', 'Pemesanan berhasil!');
    }

    public function confirmation($bookingId)
    {
        $booking = Booking::with(['film', 'studio', 'seats'])->findOrFail($bookingId);
        return view('booking.confirmation', compact('booking'));
    }
}
