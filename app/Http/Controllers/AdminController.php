<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Film;
use App\Models\User;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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


public function verifyPayment($bookingId)
{
    try {
        DB::beginTransaction();

        Log::info('🎯 === VERIFY PAYMENT METHOD DIPANGGIL ===', [
            'booking_id' => $bookingId,
            'admin_user' => auth()->user()->name,
            'time' => now()
        ]);

        $booking = Booking::with(['seats', 'studio', 'film'])->findOrFail($bookingId);

        Log::info('📦 BOOKING DATA BEFORE:', [
            'id' => $booking->id,
            'film' => $booking->film->title,
            'current_payment_status' => $booking->payment_status,
            'current_status' => $booking->status,
            'seats' => $booking->seats->pluck('seat_code')
        ]);

        // 1. Update booking status saja
        $booking->payment_status = 'verified';
        $booking->status = 'confirmed';
        $booking->paid_at = now();
        $booking->save();

        Log::info('✅ BOOKING STATUS UPDATED:', [
            'new_payment_status' => $booking->payment_status,
            'new_status' => $booking->status
        ]);

        // 2. ⬇️ JANGAN UPDATE seats.is_available LAGI! ⬇️
        // HAPUS code ini sepenuhnya:
        // $seatIds = $booking->seats->pluck('id')->toArray();
        // if (!empty($seatIds)) {
        //     $updated = DB::table('seats')
        //         ->whereIn('id', $seatIds)
        //         ->update(['is_available' => 0]);
        // }

        Log::info('💡 SEATS AVAILABILITY TIDAK DIUPDATE - Biarkan untuk film lain');

        DB::commit();

        Log::info('🎉 VERIFY PAYMENT COMPLETED SUCCESSFULLY');

        return redirect()->route('admin.bookings')
            ->with('success', 'Pembayaran berhasil diverifikasi! Kursi sekarang terkunci untuk film ini.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('💥 VERIFY PAYMENT ERROR: ' . $e->getMessage());
        return back()->with('error', 'Gagal: ' . $e->getMessage());
    }
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
    public function createFilm()
    {
        return view('admin.film-create');
    }

    public function storeFilm(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'genre' => 'required|string|max:255',
        'duration' => 'required|string|max:50',
        'price' => 'required|numeric|min:0',
        'description' => 'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        // ✅ TIDAK ADA validasi 'status' di sini
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('film-posters', 'public');
    }

    // ✅ PASTIKAN status = 'playing'
    Film::create([
        'title' => $request->title,
        'genre' => $request->genre,
        'duration' => $request->duration,
        'price' => $request->price,
        'description' => $request->description,
        'status' => 'playing', // ✅ INI YANG HARUS 'playing'
        'image' => $imagePath
    ]);

    return redirect()->route('admin.films')
        ->with('success', 'Film berhasil ditambahkan ke SEDANG TAYANG! User bisa langsung pesan tiket.');
}

    public function editFilm($id)
    {
        $film = Film::findOrFail($id);
        return view('admin.film-edit', compact('film'));
    }

    public function updateFilm(Request $request, $id)
    {
        $film = Film::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'duration' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'status' => 'required|in:playing,upcoming,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'title' => $request->title,
            'genre' => $request->genre,
            'duration' => $request->duration,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->status,
        ];

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image
            if ($film->image && Storage::disk('public')->exists($film->image)) {
                Storage::disk('public')->delete($film->image);
            }

            $data['image'] = $request->file('image')->store('film-posters', 'public');
        }

        $film->update($data);

        return redirect()->route('admin.films')
            ->with('success', 'Film berhasil diperbarui!');
    }

    public function destroyFilm($id)
    {
        $film = Film::findOrFail($id);

        // Check if film has bookings
        if ($film->bookings()->count() > 0) {
            return redirect()->route('admin.films')
                ->with('error', 'Tidak bisa menghapus film yang sudah memiliki booking!');
        }

        // Delete film image
        if ($film->image && Storage::disk('public')->exists($film->image)) {
            Storage::disk('public')->delete($film->image);
        }

        $film->delete();

        return redirect()->route('admin.films')
            ->with('success', 'Film berhasil dihapus!');
    }

    public function users()
    {
        $users = User::withCount('bookings')->latest()->get();
        return view('admin.users', compact('users'));

    }

    public function destroyUser(User $user)
    {

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account.');
        }


        Booking::where('user_id', $user->id)->delete();

        
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }



}
