<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('home');
});

// Route default Breeze (login, register, dsb)
require __DIR__.'/auth.php';

// Route khusus pelanggan (butuh login)
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [FilmController::class, 'index'])->name('home');
    Route::get('/films/{id}', [FilmController::class, 'show'])->name('films.show');

    // Update routes booking sesuai dengan controller kita
    Route::get('/films/{id}/book', [BookingController::class, 'showBookingPage'])->name('films.book');
    Route::get('/studios/{studioId}/seats', [BookingController::class, 'getSeats']);
    Route::post('/bookings', [BookingController::class, 'bookSeats'])->name('bookings.store');
    Route::get('/bookings/{id}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/my-ticket', [BookingController::class, 'myTicket'])->name('booking.myTicket');

});
