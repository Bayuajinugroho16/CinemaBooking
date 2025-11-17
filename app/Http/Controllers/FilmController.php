<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // ✅ PERBAIKI: Filter berdasarkan STATUS, bukan secara acak
        $films = Film::where('status', 'playing')->get(); // SEDANG TAYANG
        $films_other = Film::where('status', 'other')->get(); // PILIHAN LAINNYA
        $upcoming_films = Film::where('status', 'upcoming')->get(); // SEGERA TAYANG

        return view('home', compact('films', 'films_other', 'upcoming_films'));
    }

    public function show($id)
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $film = Film::findOrFail($id);
        return view('films.show', compact('film'));
    }
}
