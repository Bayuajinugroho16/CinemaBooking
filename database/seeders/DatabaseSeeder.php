<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create user tanpa factory
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password', // Akan di-hash otomatis oleh mutator
            'role' => 'customer'
        ]);

        // Panggil seeder lainnya
        $this->call([
            FilmSeeder::class,
            StudioSeeder::class,
        ]);
    }
}
