<?php
// app/Http/Controllers/FilmController.php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function index()
    {
        // Untuk demo, kita bagi film secara acak
        $allFilms = Film::all();

        // Film yang sedang tayang (ambil 4 film pertama)
        $films = $allFilms->take(4);

        // Film lainnya (ambil 6 film berikutnya)
        $films_other = $allFilms->slice(4, 6);

        // Film yang akan datang (ambil sisanya)
        $upcoming_films = $allFilms->slice(10);

        return view('films.index', compact('films', 'films_other', 'upcoming_films'));
    }

    public function show($id)
    {
        $film = Film::findOrFail($id);
        return view('films.show', compact('film'));
    }
}
