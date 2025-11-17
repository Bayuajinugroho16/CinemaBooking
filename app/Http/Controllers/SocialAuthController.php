<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user sudah ada
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Login user yang sudah ada
                Auth::login($user);
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                    'email_verified_at' => now(),
                ]);

                Auth::login($user);
            }

            return redirect()->intended('/home');

        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google login failed!');
        }
    }
}
