<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SocialAuthController;

// ⬇️ TEST ROUTES - TARUH DI PALING ATAS ⬇️
Route::get('/test', function () {
    return "🎉 TEST ROUTE WORKS! Laravel is running.";
});

// Root route
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

// ✅ GOOGLE AUTH ROUTES - PAKAI NAMA BARU
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
// Route default Breeze (login, register, dsb)
require __DIR__.'/auth.php';
// Debug route - tambahkan di atas route lainnya
Route::get('/debug-auth', function () {
    return response()->json([
        'is_authenticated' => auth()->check(),
        'user_id' => auth()->check() ? auth()->id() : null,
        'user_name' => auth()->check() ? auth()->user()->name : null,
        'is_admin' => auth()->check() ? auth()->user()->is_admin : null,
        'session_id' => session()->getId()
    ]);
});
// Route khusus pelanggan (butuh login)
Route::middleware(['auth'])->group(function () {

    Route::get('/home', function () {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return app(FilmController::class)->index();
    })->name('home');
     Route::get('/films/{id}', function ($id) {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return app(FilmController::class)->show($id);
    })->name('films.show');

        Route::get('/films', [FilmController::class, 'index'])->name('films.index');
    Route::get('/films/{id}', [FilmController::class, 'show'])->name('films.show');

    // Booking routes
    Route::get('/films/{id}/book', [BookingController::class, 'showBookingPage'])->name('films.book');
    Route::get('/studios/{studioId}/seats', [BookingController::class, 'getSeats']);
    Route::post('/bookings', [BookingController::class, 'bookSeats'])->name('bookings.store');

    // Payment routes
    Route::get('/booking/{id}/payment', [BookingController::class, 'payment'])->name('booking.payment');
    Route::get('/booking/{id}/pending', [BookingController::class, 'pending'])->name('booking.pending');
    Route::get('/bookings/{id}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::get('/my-ticket', [BookingController::class, 'myTicket'])->name('booking.myTicket');

    // **TAMBAHKAN ROUTE BARU INI** ↓
    Route::get('/my-bookings/{id}', [BookingController::class, 'showUserBooking'])->name('user.booking.show');
    Route::get('/my-bookings/{id}/payment-proof', [BookingController::class, 'showPaymentProof'])->name('user.booking.payment-proof');
    Route::post('/my-bookings/{id}/upload-proof', [BookingController::class, 'uploadPaymentProof'])->name('user.booking.upload-proof');
    // **SAMPAI SINI** ↑

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Route khusus ADMIN saja
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{id}', [AdminController::class, 'showBooking'])->name('booking.show');
    Route::delete('/bookings/{id}', [AdminController::class, 'destroyBooking'])->name('booking.destroy');
    Route::post('/bookings/{id}/verify', [AdminController::class, 'verifyPayment'])->name('booking.verify');
    Route::post('/bookings/{id}/reject', [AdminController::class, 'rejectPayment'])->name('booking.reject');
    Route::get('/bookings/{id}/payment-proof', [AdminController::class, 'viewPaymentProof'])->name('booking.payment-proof');

    Route::get('/films', [AdminController::class, 'films'])->name('films');
    Route::get('/films/create', [AdminController::class, 'createFilm'])->name('films.create');
    Route::post('/films', [AdminController::class, 'storeFilm'])->name('films.store');
    Route::get('/films/{id}/edit', [AdminController::class, 'editFilm'])->name('films.edit');
    Route::put('/films/{id}', [AdminController::class, 'updateFilm'])->name('films.update');
    Route::delete('/films/{id}', [AdminController::class, 'destroyFilm'])->name('films.destroy');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});
