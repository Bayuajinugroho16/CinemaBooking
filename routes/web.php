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

    // Booking routes
    Route::get('/films/{id}/book', [BookingController::class, 'showBookingPage'])->name('films.book');
    Route::get('/studios/{studioId}/seats', [BookingController::class, 'getSeats']);
    Route::post('/bookings', [BookingController::class, 'bookSeats'])->name('bookings.store');

    // Payment routes - TAMBAHKAN INI
    Route::get('/booking/{id}/payment', [BookingController::class, 'payment'])->name('booking.payment'); // ← INI YANG DITAMBAH
    Route::post('/booking/{id}/upload-payment', [BookingController::class, 'uploadPayment'])->name('booking.uploadPayment');
    Route::get('/booking/{id}/pending', [BookingController::class, 'pending'])->name('booking.pending');
    Route::get('/bookings/{id}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::get('/my-ticket', [BookingController::class, 'myTicket'])->name('booking.myTicket');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin routes
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::get('/bookings/{id}', [AdminController::class, 'showBooking'])->name('admin.booking.show');
    Route::post('/bookings/{id}/verify', [AdminController::class, 'verifyPayment'])->name('admin.booking.verify');
    Route::post('/bookings/{id}/reject', [AdminController::class, 'rejectPayment'])->name('admin.booking.reject');
    Route::get('/bookings/{id}/payment-proof', [AdminController::class, 'viewPaymentProof'])->name('admin.booking.payment-proof');
    Route::get('/films', [AdminController::class, 'films'])->name('admin.films');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');


});
